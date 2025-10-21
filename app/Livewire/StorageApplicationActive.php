<?php

namespace App\Livewire;

use App\Models\ScanLog;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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

    public $modalData;

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

    public function checkin($application)
    {
        try {
            if ($application->qrCode->status === 'checked_in') {
                $this->dispatch('show_toast', message: "This storage has already been checked in.", type: "fail");
                return;
            }

            ScanLog::create([
                'qr_code_id' => $application->qrCode->id,
                'event_type' => 'check_in'
            ]);

            $application->qrCode->update([
                'status' => 'checked_in',
                'scanned_count' => $application->qrCode->scanned_count + 1
            ]);

            if ($application->locker_id) {
                $application->locker->update(['status' => 'occupied']);
            }
            if ($application->open_area_id) {
                $application->openArea->update(['status' => 'occupied']);
            }

            $this->dispatch('show_toast', message: "Check in successfully!", type: "success");
        } catch (\Exception $e) {
            Log::error("Checkin Error: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Error during check in.", type: "fail");
        }
    }

    public function checkout($application)
    {
        try {
            if ($application->qrCode->status !== 'checked_in') {
                $this->dispatch('show_toast', message: "This storage is not checked in yet.", type: "fail");
                return;
            }
            if ($application->qrCode->status === 'checked_out') {
                $this->dispatch('show_toast', message: "This storage has already been checked out.", type: "fail");
                return;
            }

            ScanLog::create([
                'qr_code_id' => $application->qrCode->id,
                'event_type' => 'check_out'
            ]);

            $application->qrCode->update([
                'status' => 'checked_out',
                'scanned_count' => $application->qrCode->scanned_count + 1
            ]);

            if ($application->locker_id) {
                $application->locker->update(['status' => 'available']);
            }
            if ($application->open_area_id) {
                $application->openArea->update(['status' => 'available']);
            }

            $this->dispatch('show_toast', message: "Check out successfully!", type: "success");
        } catch (\Exception $e) {
            Log::error("Checkout Error: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Error during check out.", type: "fail");
        }
    }

    #[Computed()]
    public function approvedStorageApplications()
    {
        $query = StorageApplication::query()->with(['applicant', 'approver', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom', 'qrCode.scanLogs'])->where('storage_application_status', 'approved');

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

        // TODO: Not yet checked in/out filters

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

    public function placeholder()
    {
        return view('livewire.placeholder.table');
    }
    public function render()
    {
        return view('livewire.storage-application-active');
    }
}
