<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpenArea extends Model
{
    protected $fillable = [
        'storage_room_id',
        // 'estimated_storage_capacity',
        // 'occupied_storage_capacity',
        'area_status',
    ];

    // protected $casts = [
    //     'estimated_storage_capacity' => 'decimal:2',
    //     'occupied_storage_capacity' => 'decimal:2',
    // ];

    // Eloquent Model Relationship
    public function storageRoom()
    {
        return $this->belongsTo(StorageRoom::class, 'storage_room_id');
    }

    public function storageApplications()
    {
        return $this->hasMany(StorageApplication::class, 'open_area_id');
    }

}
