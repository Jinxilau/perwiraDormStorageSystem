<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissingReport extends Model
{
    protected $fillable = [
        'status',
        'last_seen_type',
        'last_seen_date',
        'last_seen_other',
        'discovered_missing_type',
        'discovered_missing_date',
        'discovered_missing_other',
        'witnesses_and_info',
        'qr_code_id',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }
    public function storedItems()
    {
        return $this->hasMany(StoredItem::class);
    }
}
