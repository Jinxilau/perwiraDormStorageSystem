<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageApplication extends Model
{
    protected $fillable = [
        'storage_application_status',
        'expected_check_in',
        'purpose',
        'applicant_id',
        'approver_id',
        'open_area_id',
        'locker_id',
    ];

    protected $casts = [
        'storage_application_date' => 'date',
        'expected_check_in' => 'datetime',
    ];

    // Eloquent Model Relationship
    public function applicant()
    {
        return $this->belongsTo(User::class);
    }
    public function approvers()
    {
        return $this->belongsToMany(User::class);
    }
    public function openArea()
    {
        return $this->belongsTo(OpenArea::class);
    }
    public function locker()
    {
        return $this->belongsTo(Locker::class);
    }
    public function storedItems()
    {
        return $this->hasMany(StoredItem::class);
    }
    public function qrCode() 
    {
        return $this->hasOne(QRCode::class);
    }
}
