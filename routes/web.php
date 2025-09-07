<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Livewire\ManageStorageApplication;
use App\Livewire\StaffDashboard;
use App\Livewire\StorageApplication;
use App\Livewire\StorageApplicationActive;
use App\Livewire\StudentDashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::middleware(['auth', EnsureUserIsApproved::class, CheckRole::class . ':student'])->group(function () {
    Route::get('/student/dashboard', StudentDashboard::class)->name('student_dashboard');
    Route::get('/student/storage', StorageApplication::class)->name('storage');
});

Route::middleware(['auth', CheckRole::class . ':staff'])->group(function () {
    Route::get('/staff/dashboard', StaffDashboard::class)->name('staff_dashboard');
    Route::get('/staff/storage/pending', ManageStorageApplication::class)->name('manage_storage_pending');
    Route::get('/staff/storage/active', StorageApplicationActive::class)->name('manage_storage_active');
    // Route::get('/staff/storage/rejected', )->name('manage_storage_rejected');
});

Route::middleware(['auth', CheckRole::class . ':administrator'])->group(function () {

});

require __DIR__ . '/auth.php';
