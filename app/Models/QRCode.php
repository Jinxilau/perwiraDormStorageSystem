<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    protected $fillable = [
        'qr_content',
        'qr_status',
        'scanned_count',
        'qrcode_url',
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
        return $this->belongsTo(StorageApplication::class);
    }
    public function missingReports()
    {
        return $this->hasMany(MissingReport::class);
    }
    public function scanLogs() {
        return $this->hasMany(ScanLog::class);
    }
}
