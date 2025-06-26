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

    // Eloquent Relationship Model
    public function openAreas()
    {
        return $this->hasOne(OpenArea::class);
    }
    public function lockers()
    {
        return $this->hasMany(Locker::class);
    }

}
