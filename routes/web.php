<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Livewire\AdminDashboard;
use App\Livewire\ManageStorageApplication;
use App\Livewire\ManageStorageRoom;
use App\Livewire\ManageUser;
use App\Livewire\ScanQr;
use App\Livewire\StaffDashboard;
use App\Livewire\StorageApplication;
use App\Livewire\StorageApplicationActive;
use App\Livewire\StorageApplicationReject;
use App\Livewire\StudentDashboard;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.auth.login')->name('login');

Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::view('/role-error', 'role_error')->name('role_error');


// Student
Route::middleware(['auth', EnsureUserIsApproved::class, CheckRole::class . ':student'])->group(function () {
    Route::get('/student/dashboard', StudentDashboard::class)->name('student_dashboard'); # Use livewire as full page instead
    Route::get('/student/storage', StorageApplication::class)->name('storage');
});

// Staff
Route::middleware(['auth', CheckRole::class . ':staff'])->group(function () {
    Route::get('/staff/dashboard', StaffDashboard::class)->name('staff_dashboard');
});

// Administrator
Route::middleware(['auth', CheckRole::class . ':administrator'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin_dashboard');
    Route::get('/admin/user', ManageUser::class)->name('manage_user');
});

// Staff/Admin
Route::middleware(['auth', CheckRole::class . ':staff,administrator'])->group(function () {
    Route::get('/storage/pending', ManageStorageApplication::class)->name('manage_storage_pending');
    Route::get('/storage/active', StorageApplicationActive::class)->name('manage_storage_active');
    Route::get('/storage/rejected', StorageApplicationReject::class)->name('manage_storage_rejected');
    Route::get('/room/{room?}', ManageStorageRoom::class)->name('storage_room');
    Route::get('/scanqr', ScanQr::class)->name('scan_qr');
});

require __DIR__ . '/auth.php';
