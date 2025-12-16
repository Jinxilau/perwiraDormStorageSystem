<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['academic_year', 'semester_no', 'start_date', 'end_date', 'is_current'];

    public function storageApplications()
    {
        return $this->hasMany(StorageApplication::class, 'semester_id');
    }
}
