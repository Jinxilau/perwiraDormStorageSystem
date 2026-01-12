<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Locker;
use App\Models\MissingReport;
use App\Models\OpenArea;
use App\Models\ScanLog;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
#[Lazy()]
class AdminDashboard extends Component
{
    // Properties for stats
    public $pendingApplicationsCount = 0;
    public $activeStorageUsers = 0;
    public $pendingMissingReports = 0;

    // Locker
    public $lockerCapacityPercentage = 0;
    public $occupiedLockers = 0; # Applications currently checked in
    public $totalLockers = 0; # Fixed capacity
    public $totalItemsInLockers = 0; # Physical items in lockers

    // Open Area
    public $occupiedOpenAreasCount = 0; # Applications currently checked in
    public $totalItemsInOpenArea = 0; # Physical items in open areas

    public $totalActiveItems = 0; // Combined count of items in lockers and open areas

    protected $listeners = ['notificationReceived' => '$refresh'];

    public function mount()
    {
        $this->loadStatistics();
    }

    // Load all metrics for the dashboard
    public function loadStatistics()
    {
        // Get current semester ID (adjust based on your semester logic)
        $currentSemesterId = $this->getCurrentSemesterId();

        // Pending applications count
        $this->pendingApplicationsCount = StorageApplication::where('storage_application_status', 'pending')
            ->where('semester_id', $currentSemesterId)
            ->count();

        // Active storage users (approved applications)
        $this->activeStorageUsers = StorageApplication::where('storage_application_status', 'approved')
            ->where('semester_id', $currentSemesterId)
            ->whereHas('qrCode', function ($query) {
                // Only count those who are currently checked_in
                // This automatically excludes 'not_scanned' and 'checked_out'
                $query->whereIn('status', ['not_scanned', 'checked_in']);
            })
            ->distinct('applicant_id')
            ->count('applicant_id');

        // Pending missing reports (pending + investigating)
        $this->pendingMissingReports = MissingReport::whereIn('status', ['pending', 'investigating'])
            ->where('semester_id', $currentSemesterId)
            ->count();

        // 1. Fixed Capacity (Lockers Only)
        $this->totalLockers = Locker::count();

        // 2. Occupied Lockers (Approved AND currently Checked-In)
        $this->occupiedLockers = StorageApplication::where('storage_application_status', 'approved')
            ->where('semester_id', $currentSemesterId)
            ->whereNotNull('locker_id')
            ->whereHas('qrCode', function ($query) {
                $query->where('status', 'checked_in');
            })
            ->count();

        // 3. Open Area Application (Just a count, no capacity percentage)
        $this->occupiedOpenAreasCount = StorageApplication::where('storage_application_status', 'approved')
            ->where('semester_id', $currentSemesterId)
            ->whereNotNull('open_area_id')
            ->whereHas('qrCode', function ($query) {
                $query->where('status', 'checked_in');
            })
            ->count();

        // 4. Calculate Percentage based ONLY on Lockers
        $this->lockerCapacityPercentage = $this->totalLockers > 0
            ? round(($this->occupiedLockers / $this->totalLockers) * 100)
            : 0;

        // Count of physical items in Lockers
        $this->totalItemsInLockers = StoredItem::whereHas('storageApplication', function ($query) use ($currentSemesterId) {
            $query->where('semester_id', $currentSemesterId)
                ->whereNotNull('locker_id')
                ->whereHas('qrCode', function ($q) {
                    $q->where('status', 'checked_in');
                });
        })->count();

        // Count of physical items in Open Areas
        $this->totalItemsInOpenArea = StoredItem::whereHas('storageApplication', function ($query) use ($currentSemesterId) {
            $query->where('semester_id', $currentSemesterId)
                ->whereNotNull('open_area_id')
                ->whereHas('qrCode', function ($q) {
                    $q->where('status', 'checked_in');
                });
        })->count();

        // 5. Overall "Active Items" count for the dashboard
        $this->totalActiveItems = $this->totalItemsInLockers + $this->totalItemsInOpenArea;
    }

    private function getCurrentSemesterId()
    {
        // 1. Try to find by the flag first
        $semester = Semester::where('is_current', true)->first();

        // 2. Fallback: If no flag is set, try to find by the current date
        if (!$semester) {
            $semester = Semester::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();
        }

        return $semester ? $semester->id : null;
    }

    // Notfication Section
    public function markAsRead($notificationId)
    {
        $noti = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($noti) {
            $noti->markAsRead();
            $this->dispatch('show_toast', message: "Notification marked as read.", type: "success");
        } else {
            $this->dispatch('show_toast', message: "Notification not found.", type: 'fail');
        }
    }

    #[Computed]
    public function recentScanLogs()
    {
        return ScanLog::with([
            'qrCode.storageApplication.applicant',
            'semester'
        ])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function recentActivityLogs()
    {
        return ActivityLog::with(['user', 'semester', 'subject'])
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function recentApplications()
    {
        $currentSemesterId = $this->getCurrentSemesterId();

        return StorageApplication::with(['applicant', 'locker', 'openArea'])
            ->where('semester_id', $currentSemesterId)
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function recentMissingReports()
    {
        $currentSemesterId = $this->getCurrentSemesterId();

        return MissingReport::with([
            'storedItems.storageApplication' => function ($q) {
                $q->with(['openArea.storageRoom', 'locker.storageRoom', 'applicant', 'semester']);
            },
        ])->where('semester_id', $currentSemesterId)->latest()->take(5)->get();
    }

    #[Computed()]
    public function notifications()
    {
        return Auth::user()->notifications()->latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
