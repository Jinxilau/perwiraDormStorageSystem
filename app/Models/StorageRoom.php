<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageRoom extends Model
{
    protected $fillable = [
        'room_name',
        'storage_location',
        'room_status',
    ];

    /**
     * Determine if the room can be safely deleted.
     */
    public function canDelete(): bool
    {
        $activeStatuses = ['pending', 'approved'];

        // Optimization: Use 'orHas' logic or check sequentially
        $hasActiveLockerApps = $this->lockers()
            ->whereHas('storageApplications', fn($q) => $q->whereIn('storage_application_status', $activeStatuses))
            ->exists();

        if ($hasActiveLockerApps) return false;

        $hasActiveOpenAreaApps = $this->openAreas()
            ->whereHas('storageApplications', fn($q) => $q->whereIn('storage_application_status', $activeStatuses))
            ->exists();

        return !$hasActiveOpenAreaApps;
    }

    // Eloquent Relationship Model
    public function openAreas()
    {
        return $this->hasOne(OpenArea::class);
    }
    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }


    public function getAvailableStorageAttribute(): bool
    {
        return $this->availableLockers()->count() > 0 || $this->openAreas()->where('area_status', 'available')->exists();
    }
}
