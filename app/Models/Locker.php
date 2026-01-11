<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    protected $fillable = [
        'storage_room_id',
        'code',
        'size',
        'status'
    ];

    // Check if the locker has any stored items， true means it cannot be marked as available
    public function canBeMarkedAvailable(): bool
    {
        return $this->storageApplications()->where('storage_application_status', 'approved')->whereHas('qrCode', function ($query) {
            // The locker is only "unavailable" if the QR status is NOT checked_out
            $query->where('status', '!=', 'checked_out');
        })->exists();
    }

    public function canBeDeleted(): bool
    {
        return !$this->storageApplications()
            ->whereIn('storage_application_status', ['pending', 'approved'])
            ->exists();
    }

    // Eloquent Model Relationship
    public function storageRoom()
    {
        return $this->belongsTo(StorageRoom::class, 'storage_room_id');
    }
    public function storageApplications()
    {
        return $this->hasMany(StorageApplication::class, 'locker_id');
    }
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
