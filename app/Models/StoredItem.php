<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoredItem extends Model
{
    protected $fillable = [
        'item_name',
        'item_type',
        'item_condition',
        'item_description',
        'estimated_size',
        'storage_application_id',
        'missing_report_id',
        'item_photo_path',
    ];

    // Run automatic assignment of item_group based on item_type
    protected static function booted()
    {
        static::creating(function ($item) {
            $item->item_condition = match ($item->item_type) {
                'electronic', 'home_appliance' => 'fragile',
                'furniture', 'luggage' => 'bulky',
                'container' => 'boxed',
                default => 'misc',
            };
        });
    }

    public function storageApplication()
    {
        return $this->belongsTo(StorageApplication::class, 'storage_application_id');
    }
    public function missingReport()
    {
        return $this->belongsTo(MissingReport::class, 'missing_report_id');
    }
}
