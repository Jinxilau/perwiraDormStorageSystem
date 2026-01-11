<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        // 'semester_id',
        'matric_no',
        'name',
        'email',
        'password',
        'phone_number',
        'gender',
        'year_of_study',
        'application_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Eloquent Model Relationship
    // public function semester()
    // {
    //     return $this->belongsTo(Semester::class, 'semester_id');
    // }
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }
    public function applyStorage()
    {
        return $this->hasMany(StorageApplication::class, 'applicant_id');
    }
    public function reviewApplications()
    {
        return $this->hasMany(StorageApplication::class, 'approver_id');
    }
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}
