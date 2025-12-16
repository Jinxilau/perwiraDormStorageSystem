<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissingReport extends Model
{
    protected $casts = [
        'last_seen_date' => 'date',
        'discovered_missing_date' => 'date'
    ];

    protected $fillable = [
        'status',
        'last_seen_type',
        'last_seen_date',
        'last_seen_range',
        'last_seen_location',
        'discovered_missing_type',
        'discovered_missing_date',
        'discovered_missing_range',
        'witnesses_and_info',
        'admin_note',
        'semester_id',
        // 'qr_code_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($missingReport) {
            // Only assign if it wasn't manually passed
            if (is_null($missingReport->semester_id)) {
                $missingReport->semester_id = Semester::where('is_current', true)->value('id');
            }
        });
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'missing_report_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
