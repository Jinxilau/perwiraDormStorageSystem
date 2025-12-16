<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'subject_type',
        'action',
        'description',
        'ip_address',
        'semester_id',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($activityLog) {
            // Only assign if it wasn't manually passed
            if (is_null($activityLog->semester_id)) {
                $activityLog->semester_id = Semester::where('is_current', true)->value('id');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
