<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
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
    public $sortDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->storageType = '';
        $this->sortDate = '';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    #[Computed()]
    public function rejectedStorageApplications()
    {
        $query = StorageApplication::query()->with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'rejected');

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

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.storage-application-reject');
    }
}
