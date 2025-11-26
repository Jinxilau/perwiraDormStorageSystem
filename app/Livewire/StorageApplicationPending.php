<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\QRCode;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use App\Notifications\ApproveStorageApplicationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $this->sortDate = '';
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
            } elseif ($application->open_area_id) {
                $application->openArea->update(['status' => 'reserved']);
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
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'approve_storage_application',
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
            StorageApplication::whereIn('id', $this->selectedApplications)->update([
                'storage_application_status' => 'rejected',
                'note' => $this->rejectionNote,
                'approver_id' => Auth::user()->id,
            ]);

            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function rejectApplication($id)
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            $application = StorageApplication::findOrFail($id);

            $application->update([
                'storage_application_status' => 'rejected',
                'note' => $this->rejectionNote,
                'approver_id' => Auth::user()->id
            ]);

            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
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
        $query = StorageApplication::query()->where('storage_application_status', 'pending')->with(['applicant', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom']);

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
            $query->whereBetween('create_at', [$this->fromRange, $this->toRange]);
        } elseif ($this->fromRange) {
            $query->whereDate('create_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('create_at', '<=', $this->toRange);
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

    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }

    #[Computed()]
    public function countTotalPending()
    {
        return StorageApplication::where('storage_application_status', 'pending')->count();
    }

    #[Computed()]
    public function countLocker()
    {
        return StorageApplication::where('storage_application_status', 'pending')->whereNotNull('locker_id')->count();
    }

    #[Computed()]
    public function countOpenArea()
    {
        return StorageApplication::where('storage_application_status', 'pending')->whereNotNull('open_area_id')->count();
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
