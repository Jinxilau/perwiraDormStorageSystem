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

    // Eloquent Model Relationship
    public function storageRoom()
    {
        return $this->belongsTo(StorageRoom::class, 'storage_room_id');
    }
    public function storageApplications()
    {
        return $this->hasMany(StorageApplication::class, 'locker_id');
    }

    // public function storageApplications()
    // {
    //     return $this->hasMany(StorageApplication::class, 'locker_id', 'locker_id');
    // }

}
