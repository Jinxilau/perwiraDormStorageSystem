<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['academic_year', 'semester_no', 'start_date', 'end_date', 'is_current'];

    protected $casts = [
        // This ensures NULL is converted to false when you access $semester->is_current
        'is_current' => 'boolean',
    ];


    // Relations
    public function storageApplications()
    {
        return $this->hasMany(StorageApplication::class, 'semester_id');
    }
    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class, 'semester_id');
    }
    public function missingReports()
    {
        return $this->hasMany(MissingReport::class, 'semester_id');
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'semester_id');
    }
}
