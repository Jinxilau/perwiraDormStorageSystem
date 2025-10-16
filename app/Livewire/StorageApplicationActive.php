<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StorageApplicationActive extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $room = '';
    #[Url(except: '')]
    public $storageType = '';
    #[Url(except: '')]
    public $checkinDate = '';
    #[Url(except: '')]
    public $checkoutDate = '';
    #[Url(except: '')]
    public $sortDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public $application;

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->storageType = '';
        $this->checkinDate = '';
        $this->checkoutDate = '';
        $this->sortDate = '';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    #[On('view_modal')]
    public function viewApplicationModal($id)
    {
        $this->application = $this->approvedStorageApplications->where('id', $id)->first();
        $this->dispatch('display_modal');
    }

    public function checkin()
    {
        // TODO
    }

    public function checkout()
    {
        // TODO
    }

    #[Computed()]
    public function approvedStorageApplications()
    {
        $query = StorageApplication::query()->with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom', 'qrCode.scanLogs'])->where('storage_application_status', 'approved');

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->orWhereHas("applicant", function ($userQuery) {
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

        if ($this->checkinDate) {
            $query->whereHas('qrCode.scanLogs', function ($logQuery) {
                $logQuery->where('event_type', 'check_in')->whereDate('created_at', $this->checkinDate);
            });
        }

        if ($this->checkoutDate) {
            $query->whereHas('qrCode.scanLogs', function ($logQuery) {
                $logQuery->where('event_type', 'check_out')->whereDate('created_at', $this->checkoutDate);
            });
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
    public function countTotalActive()
    {
        return StorageApplication::where('storage_application_status', 'approved')->count();
    }
    #[Computed()]
    public function countLocker()
    {
        return StorageApplication::where('storage_application_status', 'approved')->whereNotNull('locker_id')->count();
    }
    #[Computed()]
    public function countOpenArea()
    {
        return StorageApplication::where('storage_application_status', 'approved')->whereNotNull('open_area_id')->count();
    }
    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }

    public function render()
    {
        return view('livewire.storage-application-active');
    }
}
