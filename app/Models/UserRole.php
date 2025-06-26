<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = [
        'role_name',
        'role_description',
        'role_scope',
    ];

    protected $casts = [
        'role_scope' => 'array', // Cast JSON to array
    ];

    // Eloquent Model Relationship
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
