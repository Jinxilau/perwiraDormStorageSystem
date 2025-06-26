<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $fillable = [
        'event_type',
        'qr_code_id',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id');
    }
}
