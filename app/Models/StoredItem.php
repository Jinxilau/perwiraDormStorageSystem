<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoredItem extends Model
{
    protected $fillable = [
        'item_type',
        'item_description',
        'estimated_size',
        'storage_application_id',
        'missing_report_id',
        'item_photo_path',
    ];

    public function storageApplication()
    {
        return $this->belongsTo(StorageApplication::class);
    }
    public function missingReport()
    {
        return $this->belongsTo(MissingReport::class, 'missing_report');
    }

}
