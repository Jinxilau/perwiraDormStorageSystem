<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Locker;
use App\Models\QRCode;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use App\Notifications\ApproveStorageApplicationNotification;
use App\Notifications\RejectStorageApplicationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class StorageApplicationPending extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $room = '';
    #[Url(except: '')]
    public $semester = '';
    #[Url(except: 0)]
    public $conflictOnly = 0;
    #[Url(except: 0)]
    public $staleOnly = 0;
    #[Url(except: '')]
    public $storageType = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = 'latest';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10; # Pagination Amount

    public $modalData; # application id for modal
    public $modalData2; # application id for modal reject

    public $selectedApplications = []; # arr application id
    public $selectAll = false;

    public $rejectionNote = '';

    protected function resetsPage()
    {
        // If any of these properties change, go back to page 1
        return ['search', 'room', 'semester', 'storageType', 'fromRange', 'toRange', 'conflictOnly', 'staleOnly'];
    }

    public function updatedPaginationInt()
    {
        $this->reset(['selectAll', 'selectedReports']);
        $this->resetPage();
    }

    public function mount()
    {
        // Assuming your Semester model has a way to identify the active one, 
        // e.g., an 'is_active' column or scope.
        $activeSemester = Semester::where('is_current', true)->first();

        if ($activeSemester) {
            $this->semester = $activeSemester->id;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedApplications = $this->pendingStorageApplications->pluck('id')->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }

    public function updatedSelectedApplications()
    {
        $this->selectAll = count($this->selectedApplications) === $this->pendingStorageApplications->count();
    }

    public function updatedConflictOnly($value)
    {
        if ($value) {
            $this->storageType = 'locker';
        }
    }

    public function clearFilters()
    {
        // Resets all properties to their initial values defined above
        $this->reset([
            'search',
            'room',
            'semester',
            'conflictOnly',
            'staleOnly',
            'storageType',
            'fromRange',
            'toRange',
            'sortDate',
            'sortName',
            'paginationInt'
        ]);

        // If you are using WithPagination, always reset the page 
        // to avoid "No records found" on page 2+ after clearing
        $this->resetPage();

        $this->dispatch('show_toast', message: 'Filters cleared.', type: 'success');
    }

    private function processApproval(StorageApplication $application)
    {
        // Handle Locker Applications
        if ($application->locker_id) {
            // Lock the locker row to prevent others from reading it while we check
            $locker = Locker::lockForUpdate()->find($application->locker_id);

            if (!$locker) {
                throw new \Exception("Locker not found for Application ID: {$application->id}");
            }

            // CHECK: Is the locker actually available?
            if ($locker->status !== 'available') {
                throw new \Exception("Locker {$locker->code} is already occupied or under maintenance.");
            }

            // If safe, proceed to update locker status
            $locker->update(['status' => 'reserved']);
            // ... perform other assignment logic ...
        }

        //Handle Open Area Applications
        if ($application->open_area_id && $application->openArea->area_status !== 'available') {
            throw new \Exception("Open Area {$application->openArea->storageRoom->room_name} is already occupied or under maintenance.");
        }

        // 1. Create QR Code
        QRCode::create([
            'storage_application_id' => $application->id,
            'qr_token' => Str::uuid(),
            // 'qr_expires_at' => Carbon::now()->addDays(7)
        ]);

        // 3. Update Application Status
        $application->update([
            'storage_application_status' => 'approved',
            'approver_id' => Auth::id()
        ]);

        // 4. Notification
        // Wrap in try-catch if you don't want email failures to roll back the database transaction
        try {
            $application->applicant->notify(new ApproveStorageApplicationNotification($application));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        // 5. Activity Log (Standardized)
        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'approved', // Standardized action name
            'description' => "Approved application ID: {$application->id}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function approveApplication($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $application = StorageApplication::findOrFail($id);
                $this->processApproval($application);
            });

            $this->dispatch('show_toast', message: 'Application approved successfully!', type: 'success');
            $this->reset(['selectedApplications']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Approval Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: "Approval Error: {$e->getMessage()}", type: 'fail');
        }
    }

    public function bulkApprove()
    {
        if (empty($this->selectedApplications)) {
            $this->dispatch('show_toast', message: 'No applications selected.', type: 'error');
            return;
        }

        try {
            // Use eager loading (with) to prevent N+1 query issues
            $applications = StorageApplication::with(['locker.storageRoom', 'openArea.storageRoom', 'applicant'])
                ->whereIn('id', $this->selectedApplications)->where('storage_application_status', 'pending')
                ->get();

            if ($applications->isEmpty()) {
                $this->dispatch('show_toast', message: 'No applications selected.', type: 'warning');
                return;
            }
            if ($applications->count() !== count($this->selectedApplications)) {
                $this->dispatch('show_toast', message: 'Some applications are no longer pending.', type: 'error');
                return;
            }
            // --- Rule 1: same storage room ---
            $roomIds = $applications
                ->map(fn($app) => $app->storage_room?->id)
                ->unique();

            if ($roomIds->count() !== 1) {
                $this->dispatch('show_toast', message: 'Bulk approve allowed only for the same storage room.', type: 'error');
                return;
            }
            // --- Rule 2: same storage type ---
            $types = $applications->map(function ($app) {
                if ($app->locker_id && !$app->open_area_id) {
                    return 'locker';
                }

                if ($app->open_area_id && !$app->locker_id) {
                    return 'open_area';
                }

                return 'mixed';
            })->unique();

            if ($types->count() !== 1 || $types->first() === 'mixed') {
                $this->dispatch('show_toast', message: 'Bulk approve must be the same storage type.', type: 'error');
                return;
            }
            // --- Rule 3: no locker conflicts ---
            if ($types->first() === 'locker') {
                $lockerIds = $applications
                    ->pluck('locker_id')
                    ->filter()
                    ->unique();

                if ($lockerIds->count() !== $applications->count()) {
                    $this->dispatch('show_toast', message: 'Bulk approve failed: multiple applications target the same locker.', type: 'error');
                    return;
                }
            }

            DB::transaction(function () use ($applications) {
                foreach ($applications as $application) {
                    $this->processApproval($application);
                }
            });

            $this->dispatch('show_toast', message: 'Applications approved successfully!', type: 'success');

            // Only reset the selection array, usually better than resetting the whole component in bulk actions
            $this->selectedApplications = [];
            $this->dispatch('clear-selection'); // Optional: if you use a frontend library for checkboxes

        } catch (\Exception $e) {
            Log::error('Bulk Approval Failed: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Batch failed: ' . $e->getMessage(), type: 'error');
        }
    }

    private function processRejection(StorageApplication $application)
    {
        // 1. Update Application Status
        $application->update([
            'storage_application_status' => 'rejected',
            'note' => $this->rejectionNote,
            'approver_id' => Auth::id()
        ]);

        // 3. Notification (Optional but recommended)
        try {
            $application->applicant->notify(new RejectStorageApplicationNotification($application, $this->rejectionNote));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        // 4. Activity Log
        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'description' => "Rejected application ID: {$application->id}. Note: {$this->rejectionNote}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function rejectApplication($id)
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            DB::transaction(function () use ($id) {
                $application = StorageApplication::findOrFail($id);
                $this->processRejection($application);
            });

            $this->dispatch('close-modal');
            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
            $this->reset(['rejectionNote', 'selectedApplications']); // Good practice to reset inputs

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Rejection Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function bulkReject()
    {
        if (empty($this->selectedApplications)) {
            $this->dispatch('toast', type: 'error', message: 'No applications selected.');
            return;
        }

        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            // Eager load locker, openArea, and applicant to avoid N+1 queries during loop
            $applications = StorageApplication::with(['locker', 'openArea', 'applicant'])
                ->whereIn('id', $this->selectedApplications)
                ->get();

            if ($applications->isEmpty()) {
                $this->dispatch('show_toast', message: 'No applications selected.', type: 'warning');
                return;
            }

            DB::transaction(function () use ($applications) {
                foreach ($applications as $application) {
                    $this->processRejection($application);
                }
            });

            $this->dispatch('show_toast', message: 'Applications rejected successfully!', type: 'success');

            $this->selectedApplications = [];
            $this->dispatch('close-modal');
            $this->reset('rejectionNote');
        } catch (\Exception $e) {
            Log::error('Bulk Rejection Failed: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Batch failed: ' . $e->getMessage(), type: 'error');
        }
    }

    protected function getCompetingApplications(StorageApplication $application)
    {
        // Only makes sense for locker applications
        if (!$application->locker_id) {
            return collect();
        }

        return StorageApplication::with('applicant')
            ->where('locker_id', $application->locker_id)
            ->where('semester_id', $application->semester_id)
            ->where('storage_application_status', 'pending')
            ->orderBy('created_at')       // fairness
            ->get();
    }

    #[Computed()]
    public function conflictedApplicationIds()
    {
        $lockerIds = $this->pendingStorageApplications
            ->whereNotNull('locker_id')
            ->pluck('locker_id')
            ->unique();

        if ($lockerIds->isEmpty()) {
            return [];
        }

        $conflicts = StorageApplication::select('locker_id')
            ->whereIn('locker_id', $lockerIds)
            ->where('storage_application_status', 'pending')
            ->groupBy('locker_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('locker_id');

        return $this->pendingStorageApplications
            ->whereIn('locker_id', $conflicts)
            ->pluck('id')
            ->toArray();
    }

    #[Computed()]
    public function modalApplication()
    {
        if ($this->modalData === null) return null;

        return StorageApplication::with([
            'applicant',
            'storedItems',
            'openArea.storageRoom',
            'locker.storageRoom',
        ])->find($this->modalData);
    }

    #[Computed]
    public function bulkApproveDisabled()
    {
        // Optional: lightweight frontend check
        return count($this->selectedApplications) < 1;
    }

    #[Computed()]
    public function pendingStorageApplications()
    {
        $query = StorageApplication::query()->where('storage_application_status', 'pending')->with(['applicant', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom', 'semester']);

        if ($this->search) {
            $query->where(function ($q) {
                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                } else {
                    $q->whereHas('applicant', function ($userQuery) {
                        $userQuery
                            ->where('name', 'like', "%{$this->search}%")
                            ->orWhere('matric_no', 'like', "%{$this->search}%");
                    });
                }
            });
        }

        if ($this->room) {
            $query->where(function ($q) {
                $q->whereHas("openArea.storageRoom", function ($roomQuery) {
                    $roomQuery->where('id', $this->room);
                })->orWhereHas("locker.storageRoom", function ($roomQuery) {
                    $roomQuery->where('id', $this->room);
                });
            });
        }

        if ($this->semester) {
            $query->where('semester_id', $this->semester);
        }

        if ((int) $this->conflictOnly === 1) {
            $query->whereIn('locker_id', function ($subQuery) {
                $subQuery->select('locker_id')
                    ->from('storage_applications')
                    ->where('storage_application_status', 'pending')
                    ->whereNotNull('locker_id')
                    ->whereColumn('semester_id', 'storage_applications.semester_id')
                    ->groupBy('locker_id', 'semester_id')
                    ->havingRaw('COUNT(*) > 1');
            });
        }

        if ((int) $this->staleOnly === 1) {
            $query->where('created_at', '<=', Carbon::now()->subDays(7));
        }

        if ($this->storageType) {
            if ($this->storageType === 'locker') {
                $query->whereNotNull('locker_id');
            } elseif ($this->storageType === 'openArea') {
                $query->whereNotNull('open_area_id');
            }
        }

        if ($this->fromRange && $this->toRange) {
            if ($this->fromRange && $this->toRange) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->fromRange)->startOfDay(),
                    Carbon::parse($this->toRange)->endOfDay(),
                ]);
            }
        } elseif ($this->fromRange) {
            $query->where('created_at', '>=', Carbon::parse($this->fromRange)->startOfDay());
        } elseif ($this->toRange) {
            $query->where('created_at', '<=', Carbon::parse($this->toRange)->endOfDay());
        }

        if ($this->sortDate === 'latest') {
            $query->latest();
        } elseif ($this->sortDate === 'oldest') {
            $query->oldest();
        }

        if ($this->sortName) {
            $query->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'storage_applications.applicant_id'),
                $this->sortName === 'desc' ? 'desc' : 'asc'
            );
        }

        return $query->paginate($this->paginationInt);
    }

    // Metrics for dashboard cards
    #[Computed]
    public function storageCounts()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        $pending = StorageApplication::where('storage_application_status', 'pending')
            ->where('semester_id', $currentSemesterId);

        return [
            'total' => $pending->count(),
            'locker' => $pending->whereNotNull('locker_id')->count(),
            'open_area' => $pending->whereNotNull('open_area_id')->count(),
        ];
    }
    #[Computed]
    public function staleCount()
    {
        return StorageApplication::where('storage_application_status', 'pending')
            ->where('created_at', '<=', now()->subDays(7))
            ->count();
    }
    #[Computed]
    public function newTodayCount()
    {
        return StorageApplication::where('storage_application_status', 'pending')
            ->whereDate('created_at', today())
            ->count();
    }

    // Populate the filter dropdowns
    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }
    #[Computed()]
    public function semesters()
    {
        // Adjust sorting as needed (e.g., latest semester first)
        return Semester::orderBy('created_at', 'desc')->get();
    }

    public function placeholder()
    {
        return view('livewire.placeholder.table');
    }

    public function render()
    {
        return view('livewire.storage-application-pending');
    }
}
