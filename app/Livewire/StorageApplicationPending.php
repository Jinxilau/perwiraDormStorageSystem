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
    #[Url(except: '')]
    public $storageType = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10; # Pagination Amount

    public $modalData; # application id for modal
    public $modalData2; # application id for modal reject

    public $selectedApplications = []; # arr application id
    public $selectAll = false;

    public $rejectionNote = '';

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

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->storageType = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = 'latest';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    public function approveApplication($id)
    {
        try {
            $application = StorageApplication::findOrFail($id);

            QRCode::create([
                'storage_application_id' => $id,
                'qr_token' => Str::uuid(),
                'qr_expires_at' => Carbon::now()->addDays(7)
            ]);

            if ($application->locker_id) {
                $application->locker->update(['status' => 'reserved']);
            }

            $application->update([
                'storage_application_status' => 'approved',
                'approver_id' => Auth::user()->id
            ]);

            $this->dispatch('show_toast', message: 'Application approved successfully!', type: 'success');
            $this->reset();

            // Notification
            $application->applicant->notify(new ApproveStorageApplicationNotification($application));

            // Logs activity to the system
            $application->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'approved',
                'description' => "Approved application ID: {$application->id}",
                'ip_address' => request()->ip(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
            Log::error($e->getMessage());
        }
    }
    public function bulkApprove()
    {
        try {
            $applications = StorageApplication::whereIn('id', $this->selectedApplications)->get();

            foreach ($applications as $application) {
                QRCode::create([
                    'storage_application_id' => $application->id,
                    'qr_token' => Str::uuid(),
                    'qr_expires_at' => Carbon::now()->addDays(7),
                ]);

                $application->update([
                    'storage_application_status' => 'approved',
                    'approver_id' => Auth::user()->id
                ]);

                if ($application->locker_id) {
                    $application->locker->update(['status' => 'reserved']);
                } elseif ($application->open_area_id) {
                    $application->openArea->update(['status' => 'reserved']);
                }

                // Notification
                $application->applicant->notify(new ApproveStorageApplicationNotification($application));

                // Logs activity to the system
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'approve_storage_application',
                    'description' => "Approved application ID: {$application->id}",
                    'ip_address' => request()->ip(),
                ]);
            }

            $this->dispatch('show_toast', message: 'Application approved successfully!', type: 'success');
            $this->reset();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function bulkReject()
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            DB::transaction(function () {
                // 1. Find all locker IDs associated with these applications
                $lockerIds = StorageApplication::whereIn('id', $this->selectedApplications)
                    ->whereNotNull('locker_id') // Filter out applications that might be for Open Areas
                    ->pluck('locker_id');

                // 2. Reject the Applications
                StorageApplication::whereIn('id', $this->selectedApplications)->update([
                    'storage_application_status' => 'rejected',
                    'note' => $this->rejectionNote,
                    'approver_id' => Auth::user()->id,
                ]);

                // 3. Release the Lockers (Update status to 'available')
                if ($lockerIds->isNotEmpty()) {
                    Locker::whereIn('id', $lockerIds)->update(['status' => 'available']);
                }
            });

            $this->dispatch('show_toast', message: 'Applications rejected and lockers released successfully!', type: 'success');

            // Optional: Clear selection after bulk action
            $this->selectedApplications = [];
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function rejectApplication($id)
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            DB::transaction(function () use ($id) {
                $application = StorageApplication::findOrFail($id);

                // 1. Update Application Status
                $application->update([
                    'storage_application_status' => 'rejected',
                    'note' => $this->rejectionNote,
                    'approver_id' => Auth::user()->id
                ]);

                // 2. Release the Locker if one is assigned
                if ($application->locker_id) {
                    // Assuming 'available' is your status for a free locker
                    $application->locker()->update(['status' => 'available']);
                }
            });

            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
            $this->dispatch('close-modal');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    #[Computed()]
    public function pendingStorageApplications()
    {
        $query = StorageApplication::query()->where('storage_application_status', 'pending')->with(['applicant', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom', 'semester']);

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->WhereHas("applicant", function ($userQuery) {
                    $userQuery->where('name', 'like', "%" . $this->search . "%")->orWhere('matric_no', 'like', '%' . $this->search . "%");
                });
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

        if ($this->storageType) {
            if ($this->storageType === 'locker') {
                $query->whereNotNull('locker_id');
            } elseif ($this->storageType === 'openArea') {
                $query->whereNotNull('open_area_id');
            } elseif ($this->storageType === 'lockerOpenArea') {
                $query->whereNotNull('locker_id')->whereNotNull('open_area_id');
            }
        }

        if ($this->fromRange && $this->toRange) {
            $query->whereBetween('created_at', [$this->fromRange, $this->toRange]);
        } elseif ($this->fromRange) {
            $query->whereDate('created_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('created_at', '<=', $this->toRange);
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
    #[Computed()]
    public function pendingThisSemester()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        return StorageApplication::where('storage_application_status', 'pending')
            ->where('semester_id', $currentSemesterId)
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


    // TODO: Counts
    #[Computed]
    public function storageCounts()
    {
        $pending = StorageApplication::where('storage_application_status', 'pending');

        return [
            'total' => $pending->count(),
            'locker' => $pending->whereNotNull('locker_id')->count(),
            'open_area' => $pending->whereNotNull('open_area_id')->count(),
        ];
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
