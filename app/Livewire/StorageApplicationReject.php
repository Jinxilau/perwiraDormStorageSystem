<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $storageType = '';
    #[Url(except: '')]
    public $sortApplicationDate = '';
    #[Url(except: '')]
    public $rejectedDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public $modalData;

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->storageType = '';
        $this->sortApplicationDate = '';
        $this->rejectedDate = '';
        $this->sortName = '';

        $this->paginationInt = 10;
    }

    public function approveApplication($applicationId)
    {
        try {
            $application = StorageApplication::findOrFail($applicationId);

            QRCode::create([
                'storage_application_id' => $application->id,
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
            Log::error($e->getMessage());
        }
    }

    public function undoApplication($applicationId)
    {
        try {
            $application = StorageApplication::findOrFail($applicationId);

            $application->update([
                'storage_application_status' => 'pending',
                'approver_id' => null,
                'note' => null
            ]);
            $this->dispatch('show_toast', message: 'Rejection undone successfully!', type: 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    #[Computed()]
    public function rejectedStorageApplications()
    {
        $query = StorageApplication::query()->with(['applicant', 'approver', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'rejected');

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->whereHas("applicant", function ($userQuery) {
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

        if ($this->sortApplicationDate === 'latest') {
            $query->latest();
        } elseif ($this->sortApplicationDate === 'oldest') {
            $query->oldest();
        }

        if ($this->rejectedDate === 'latest') {
            $query->orderBy('updated_at', 'desc');
        } elseif ($this->rejectedDate === 'oldest') {
            $query->orderBy('updated_at', 'asc');
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
    public function countTotalRejected()
    {
        return StorageApplication::where('storage_application_status', 'rejected')->count();
    }

    #[Computed()]
    public function countTotalRejectedToday()
    {
        return StorageApplication::where('storage_application_status', 'rejected')->whereDate('created_at', now()->toDateString())->count();
    }

    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
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
