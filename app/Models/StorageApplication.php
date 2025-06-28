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
        return $this->belongsTo(User::class, 'applicant_id');
    }
    public function approvers()
    {
        return $this->belongsToMany(User::class, 'approver_id');
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
}
