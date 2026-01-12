<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $fillable = [
        'event_type',
        'qr_code_id',
        'semester_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($scanLog) {
            // Only assign if it wasn't manually passed
            if (is_null($scanLog->semester_id)) {
                $scanLog->semester_id = Semester::where('is_current', true)->value('id');
            }
        });
    }
    
    public function qrCode()
    {
        return $this->belongsTo(QRCode::class, 'qr_code_id');
    }
    
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
