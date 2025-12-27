<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    protected $fillable = [
        'storage_application_id',
        'qr_token',
        'scanned_count',
        'qr_expires_at',
        'status',
        'checkin_item_photo',
        'storage_zone',
    ];

    protected $casts = [
        'qr_expires_at' => 'datetime',
        'scanned_count' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($qrCode) {
            // 1. Fetch the application and its current semester
            // We use the ID because the relationship might not be loaded on the new instance yet
            $application = StorageApplication::with('semester')->find($qrCode->storage_application_id);

            if ($application && $application->semester) {
                // 2. Find the "Next" Semester
                // We look for the semester with the closest start_date that is greater than the current one
                $nextSemester = Semester::where('start_date', '>', $application->semester->start_date)
                    ->orderBy('start_date', 'asc')
                    ->first();

                if ($nextSemester) {
                    // Requirement: 10 days after next semester's start date
                    $qrCode->qr_expires_at = Carbon::parse($nextSemester->start_date)->addDays(10);
                } else {
                    // FALLBACK: If the admin hasn't created the next semester in the DB yet, 
                    // we default to 10 days after the *current* semester ends to prevent errors.
                    $qrCode->qr_expires_at = Carbon::parse($application->semester->end_date)->addDays(10);
                }
            }
        });
    }

    // Eloquent Relationship Model
    public function storageApplication()
    {
        return $this->belongsTo(StorageApplication::class, 'storage_application_id');
    }

    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class, 'qr_code_id');
    }
}
