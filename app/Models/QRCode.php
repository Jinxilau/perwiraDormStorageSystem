<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    protected $fillable = [
        'qr_token',
        'scanned_count',
        'storage_application_id',
        'qr_expires_at',
    ];

    protected $casts = [
        'qr_expires_at' => 'datetime',
        'scanned_count' => 'integer',
    ];

    // Eloquent Relationship Model
    public function storageApplication()
    {
        return $this->belongsTo(StorageApplication::class, 'storage_application_id');
    }
    public function missingReports()
    {
        return $this->hasMany(MissingReport::class, 'qr_code_id');
    }
    public function scanLogs() {
        return $this->hasMany(ScanLog::class, 'qr_code_id');
    }
}
