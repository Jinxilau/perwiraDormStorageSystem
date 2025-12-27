<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StorageApplication extends Model
{
    protected $appends = ['storage_room']; # Appending storage room only if they are relation loaded.

    protected $fillable = [
        'storage_application_status',
        'note',
        'applicant_id',
        'approver_id',
        'open_area_id',
        'locker_id',
        'semester_id',
    ];

    // Eloquent Model Relationship
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function openArea()
    {
        return $this->belongsTo(OpenArea::class, 'open_area_id');
    }

    public function locker()
    {
        return $this->belongsTo(Locker::class, 'locker_id');
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'storage_application_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QRCode::class, 'storage_application_id');
    }
    public function scanLogs()
    {
        return $this->hasManyThrough(
            ScanLog::class,
            QRCode::class,
            'storage_application_id', // FK on qr_codes
            'qr_code_id',             // FK on scan_logs
            'id',
            'id'
        );
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function getStorageRoomAttribute()
    {
        if ($this->relationLoaded('locker') && $this->locker?->relationLoaded('storageRoom')) {
            return $this->locker->storageRoom;
        }

        if ($this->relationLoaded('openArea') && $this->openArea?->relationLoaded('storageRoom')) {
            return $this->openArea->storageRoom;
        }

        return null;
    }

    /* 
    // To get the count
    $unclaimedCount = StorageApplication::unclaimed()->count();

    // To get the actual records
    $unclaimedItems = StorageApplication::unclaimed()->get();
    */
    public function scopeUnclaimed($query)
    {
        return $query->join('semesters as app_sem', 'storage_applications.semester_id', '=', 'app_sem.id')
            ->join('semesters as next_sem', function ($join) {
                $join->on(function ($q) {
                    // Case 1: Semester 1 → Semester 2 (same academic year)
                    $q->whereColumn('next_sem.academic_year', 'app_sem.academic_year')
                        ->whereColumn('next_sem.semester_no', DB::raw('app_sem.semester_no + 1'));
                })->orOn(function ($q) {
                    // Case 2: Semester 2 → Semester 1 (next academic year)
                    $q->whereColumn('app_sem.semester_no', DB::raw(2))
                        ->whereColumn('next_sem.semester_no', DB::raw(1))
                        ->whereRaw("CAST(SUBSTRING_INDEX(next_sem.academic_year, '/', 1) AS UNSIGNED) = CAST(SUBSTRING_INDEX(app_sem.academic_year, '/', 2) AS UNSIGNED)");
                });
            })
            ->join('q_r_codes', 'q_r_codes.storage_application_id', '=', 'storage_applications.id')
            ->where('q_r_codes.status', '!=', 'checked_out')
            // Based on the notice, items are disposed 1 week after the start of the next semester 
            ->whereDate(DB::raw('DATE_ADD(next_sem.start_date, INTERVAL 7 DAY)'), '<', Carbon::now());
    }

    // $application->is_unclaimed
    public function getIsUnclaimedAttribute(): bool
    {
        // 1. If it's already checked out, it's not "unclaimed"
        if (!$this->qrCode || $this->qrCode?->status === 'checked_out') {
            return false;
        }

        // 2. Find the "Next Semester" start date
        // This replicates your SQL logic in PHP/Eloquent
        $nextSemester = Semester::where(function ($q) {
            $q->where('academic_year', $this->semester->academic_year)
                ->where('semester_no', $this->semester->semester_no + 1);
        })
            ->orWhere(function ($q) {
                $q->where('semester_no', 1)
                    ->where('academic_year', 'like', explode('/', $this->semester->academic_year)[1] . '/%');
            })
            ->first();

        if (!$nextSemester) {
            return false;
        }

        // 3. Check if current date is 7 days past the next semester start date
        $deadline = Carbon::parse($nextSemester->start_date)->addDays(7);

        return now()->gt($deadline);
    }
}
