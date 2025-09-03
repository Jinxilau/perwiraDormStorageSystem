<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Livewire\StorageApplication;
use App\Livewire\StudentDashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::middleware(['auth', EnsureUserIsApproved::class, CheckRole::class . ':student'])->group(function() {
    Route::get('/student/dashboard', StudentDashboard::class)->name('student_dashboard');
    Route::get('/student/storage', StorageApplication::class)->name('storage');
    
});

require __DIR__.'/auth.php';
