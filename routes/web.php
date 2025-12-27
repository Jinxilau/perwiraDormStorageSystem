<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureUserIsApproved;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminNotification;
use App\Livewire\ManageMissingReport;
use App\Livewire\ManageStorageApplication;
use App\Livewire\ManageStorageRoom;
use App\Livewire\ManageUser;
use App\Livewire\MissingDetails;
use App\Livewire\MissingReport;
use App\Livewire\MyReport;
use App\Livewire\MyStorage;
use App\Livewire\ScanLog;
use App\Livewire\ScanQr;
use App\Livewire\Setting;
use App\Livewire\StaffDashboard;
use App\Livewire\StaffNotification;
use App\Livewire\StorageApplication;
use App\Livewire\StorageApplicationCreate;
use App\Livewire\StorageApplicationEdit;
use App\Livewire\StorageInsight;
use App\Livewire\StudentDashboard;
use App\Livewire\StudentNotifications;
use App\Livewire\UserDetails;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Route::view('login', 'pages.auth.login')->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::view('/role-error', 'role_error')->name('role_error');


// Student
Route::middleware(['auth', EnsureUserIsApproved::class, CheckRole::class . ':student'])->group(function () {
    Route::get('/student/dashboard', StudentDashboard::class)->name('student_dashboard');
    // Route::get('/student/storage', StorageApplication::class)->name('storage');
    Route::get('/student/storage', StorageApplicationCreate::class)->name('storage');
    Route::get('/student/missing_report', MissingReport::class)->name('missing_report');
    Route::get('/student/my_storage', MyStorage::class)->name('my_storage');
    Route::get('/student/my_storage/{application}/edit', StorageApplicationEdit::class)->name('storage_edit');
    Route::get('/student/my_report', MyReport::class)->name('my_report');
    Route::get('/student/notification', StudentNotifications::class)->name('student_notification');
    Route::view('/student/help_and_support', 'help_and_support')->name('help_and_support');
});

// Administrator
Route::middleware(['auth', CheckRole::class . ':administrator'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin_dashboard');

    Route::get('/admin/user', ManageUser::class)->name('manage_user');
    Route::get('/admin/user/{user}', UserDetails::class)->name('user_details');

    Route::get('/admin/missing_report', ManageMissingReport::class)->name('manage_missing');
    Route::get('/admin/missing_report/{report}', MissingDetails::class)->name('missing_report_detail');

    Route::get('/admin/notifications', AdminNotification::class)->name('admin_notification');
    Route::get('/admin/setting', Setting::class)->name('setting');
    Route::get('/admin/storage_insight', StorageInsight::class)->name('storage_insight');
});

// Both Staff and Admin
Route::middleware(['auth', CheckRole::class . ':staff,administrator'])->group(function () {
    // Have problem when doing full page livewire, because the layout needs to be dynamic, so lazy wont work as the layouts apply late.

    // Storage Application Management
    Route::view('/storage_applications', 'admin.manage_storage_application')->name('manage_storage_application');
    Route::get('/storage_applications/{application}', function ($application) {
        return view('admin.storage-details', ['application' => $application]);
    })->name('storage_application_detail');
    Route::get('storage_applications/{application}/checkin-out', function ($application) {
        return view('admin.check-in-application', ['application' => $application]);
    })->name('check_in_application');
    
    Route::get('/room', function () {
        return view('admin.manage_storage_room');
    })->name('storage_room');

    Route::view('/scanqr', 'admin.scan_qr')->name('scan_qr');

    Route::view('/scan_log', 'admin.scan_log')->name('scan_log');
    
    Route::view('/activity_log', 'admin.activity_log')->name('activity_log');
});

// Staff
Route::middleware(['auth', CheckRole::class . ':staff'])->group(function () {
    Route::get('/staff/dashboard', StaffDashboard::class)->name('staff_dashboard');
    Route::get('staff/notification', StaffNotification::class)->name('staff_notification');
});

require __DIR__ . '/auth.php';
