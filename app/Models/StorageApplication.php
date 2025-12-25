<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
