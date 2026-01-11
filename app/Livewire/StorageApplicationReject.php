<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\QRCode;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StorageApplicationReject extends Component
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
    public $sortApplicationDate = 'latest';
    #[Url(except: '')]
    public $rejectedDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public $modalData;
    public $modalData2; # application id for modal reject

    public $rejectionNote = '';

    protected function resetsPage()
    {
        return ['search', 'room', 'semester', 'storageType'];
    }

    public function updatedPaginationInt()
    {
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

    public function clearFilters()
    {
        // Resets all specified properties to their initial values
        $this->reset([
            'search',
            'room',
            'semester',
            'storageType',
            'sortApplicationDate',
            'rejectedDate',
            'sortName'
        ]);

        // Reset pagination to 10
        $this->paginationInt = 10;

        // Ensure the user goes back to page 1
        $this->resetPage();

        $this->dispatch('show_toast', message: 'Filters cleared.', type: 'success');
    }

    public function updateNote()
    {
        // VALIDATION: Never trust user input
        $this->validate([
            'rejectionNote' => 'required|string|max:500',
        ]);

        $application = StorageApplication::find($this->modalData2);

        if (!$application) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
            return;
        }

        $application->update([
            'note' => $this->rejectionNote
        ]);

        $this->dispatch('show_toast', message: 'Note updated successfully.', type: 'success');
        $this->dispatch('close-modal', modalId: 'editNoteModal'); // Ensure ID matches your JS listener

        // Reset state
        $this->reset('modalData2', 'rejectionNote');
    }

    public function undoRejectApplication($applicationId)
    {
        try {
            DB::transaction(function () use ($applicationId) {
                // Lock the application row for update to prevent race conditions
                $application = StorageApplication::lockForUpdate()->findOrFail($applicationId);

                // // 1. Safety Check: Is the locker still available?
                // // If the application has a specific locker assigned, we must ensure 
                // // no one else has taken it since the rejection.
                // if ($application->locker_id) {
                //     $locker = Locker::lockForUpdate()->find($application->locker_id);

                //     if ($locker->status !== 'available') {
                //         throw new \Exception("Cannot undo rejection. The assigned Locker ({$locker->id}) is no longer available.");
                //     }

                //     // 2. Re-lock the locker (Set to 'unavailable' or 'pending' depending on your flow)
                //     // This ensures no one else can book it while this app returns to pending.
                //     $locker->update(['status' => 'reserved']);
                // }

                // // Same logic for Open Area if applicable
                // if ($application->open_area_id) {
                //     $openArea = $application->openArea()->lockForUpdate()->first();
                //     if ($openArea->status !== 'available') {
                //         throw new \Exception("Cannot undo rejection. The Open Area is no longer available.");
                //     }
                //     $openArea->update(['status' => 'unavailable']);
                // }

                // 3. Reset Application Status
                $application->update([
                    'storage_application_status' => 'pending',
                    'approver_id' => null,
                    'note' => null
                ]);
            });

            $this->dispatch('show_toast', message: 'Rejection undone. Application is now pending.', type: 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // Show the specific error message (e.g., "Locker no longer available") to the user
            $this->dispatch('show_toast', message: $e->getMessage(), type: 'fail');
        }
    }

    #[Computed()]
    public function rejectedStorageApplications()
    {
        $query = StorageApplication::query()->with(['applicant', 'approver', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom', 'semester'])->where('storage_application_status', 'rejected');

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

        if ($this->storageType) {
            if ($this->storageType === 'locker') {
                $query->whereNotNull('locker_id');
            } elseif ($this->storageType === 'openArea') {
                $query->whereNotNull('open_area_id');
            }
        }

        if ($this->sortApplicationDate) {
            $query->orderBy(
                'created_at',
                $this->sortApplicationDate === 'latest' ? 'desc' : 'asc'
            );
        } elseif ($this->rejectedDate) {
            $query->orderBy(
                'updated_at',
                $this->rejectedDate === 'latest' ? 'desc' : 'asc'
            );
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
    public function countTotalRejected()
    {
        return StorageApplication::where('storage_application_status', 'rejected')->count();
    }
    #[Computed()]
    public function rejectedThisSemester()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        return StorageApplication::where('storage_application_status', 'rejected')
            ->where('semester_id', $currentSemesterId)
            ->count();
    }
    #[Computed()]
    public function countTotalRejectedToday()
    {
        return StorageApplication::where('storage_application_status', 'rejected')->whereDate('created_at', now()->toDateString())->count();
    }
    #[Computed()]
    public function countRejectedLockers()
    {
        return StorageApplication::where('storage_application_status', 'rejected')
            ->whereNotNull('locker_id')
            ->count();
    }
    #[Computed()]
    public function countRejectedOpenAreas()
    {
        return StorageApplication::where('storage_application_status', 'rejected')
            ->whereNotNull('open_area_id')
            ->count();
    }
    #[Computed()]
    public function rejectedThisWeek()
    {
        return StorageApplication::where('storage_application_status', 'rejected')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
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

    #[Computed()]
    public function modalApplication()
    {
        if (!$this->modalData) return null;

        return StorageApplication::with([
            'applicant',
            'approver',
            'storedItems',
            'openArea.storageRoom',
            'locker.storageRoom',
        ])->find($this->modalData);
    }

    #[Computed()]
    public function modalApplication2()
    {
        if (!$this->modalData2) return null;

        return StorageApplication::with([
            'applicant',
            'storedItems',
            'openArea.storageRoom',
            'locker.storageRoom',
        ])->find($this->modalData2);
    }

    public function placeholder()
    {
        return view('livewire.placeholder.table');
    }

    public function render()
    {
        return view('livewire.storage-application-reject');
    }
}
