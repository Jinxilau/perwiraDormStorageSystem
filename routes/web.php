<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::middleware(['auth', EnsureUserIsApproved::class, CheckRole::class . ':student'])->group(function() {
    Route::view('/student/dashboard', 'student.dashboard')->name('student_dashboard');
    Route::view('/student/storage_application', 'student.storage_application')->name('storage_application');
        
});

require __DIR__.'/auth.php';
