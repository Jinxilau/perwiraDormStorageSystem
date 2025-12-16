<?php

namespace App\Livewire;

use App\Models\ScanLog;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $this->sortDate = 'latest';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    public function downloadQrCode($id)
    {
        $application = StorageApplication::findOrFail($id);

        // Livewire requires 'streamDownload' for file downloads
        return response()->streamDownload(function () use ($application) {
            echo QrCode::size(500)
                ->margin(4) // <--- Adds whitespace margin (centers the QR)
                ->generate($application->qrCode->qr_token);
        }, 'qr-code.svg');
    }

    public function checkin(StorageApplication $application)
    {
        // 1. Refresh data to ensure we aren't checking a cached/stale status
        // (e.g., if another admin checked it in 2 seconds ago)
        $application->load(['qrCode', 'locker', 'openArea']);

        if (!$application->qrCode) {
            $this->dispatch('show_toast', message: "Error: No QR Code linked to this application.", type: "fail");
            return;
        }

        // 2. Logic Check
        if ($application->qrCode->status === 'checked_in') {
            $this->dispatch('show_toast', message: "This storage has already been checked in.", type: "fail");
            return;
        }

        DB::beginTransaction(); // Start Safety Net

        try {
            // 3. Create Log
            ScanLog::create([
                'qr_code_id' => $application->qrCode->id,
                'event_type' => 'check_in'
            ]);

            // 4. Update QR Status
            $application->qrCode->update([
                'status' => 'checked_in',
                'scanned_count' => $application->qrCode->scanned_count + 1
            ]);

            // 5. Update Room Status
            if ($application->locker) {
                $application->locker->update(['status' => 'occupied']);
            }
            if ($application->openArea) {
                $application->openArea->update(['status' => 'occupied']);
            }

            DB::commit(); // Save everything

            $this->dispatch('show_toast', message: "Check in successfully!", type: "success");
        } catch (\Exception $e) {
            DB::rollBack(); // Undo changes if error occurs
            Log::error("Checkin Error: " . $e->getMessage());
            $this->dispatch('show_toast', message: "System error during check-in.", type: "fail");
        }
    }

    public function checkout(StorageApplication $application)
    {
        $application->load(['qrCode', 'locker', 'openArea']);

        if (!$application->qrCode) {
            $this->dispatch('show_toast', message: "Error: No QR Code found.", type: "fail");
            return;
        }

        // 1. Logic Checks (Order Matters!)
        if ($application->qrCode->status === 'checked_out') {
            $this->dispatch('show_toast', message: "This storage has already been checked out.", type: "fail");
            return;
        }

        if ($application->qrCode->status !== 'checked_in') {
            // If it's 'pending' or 'new', they can't check out yet.
            $this->dispatch('show_toast', message: "Item must be Checked In before it can be Checked Out.", type: "fail");
            return;
        }

        DB::beginTransaction();

        try {
            ScanLog::create([
                'qr_code_id' => $application->qrCode->id,
                'event_type' => 'check_out'
            ]);

            $application->qrCode->update([
                'status' => 'checked_out',
                'scanned_count' => $application->qrCode->scanned_count + 1
            ]);

            if ($application->locker) {
                $application->locker->update(['status' => 'available']);
            }
            if ($application->openArea) {
                $application->openArea->update(['status' => 'available']);
            }

            DB::commit();

            $this->dispatch('show_toast', message: "Check-out successful!", type: "success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            $this->dispatch('show_toast', message: "System error during check-out.", type: "fail");
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

    #[Computed]
    public function activeCounts()
    {
        return StorageApplication::selectRaw("
            COUNT(*) as total,
            COUNT(locker_id) as locker,
            COUNT(open_area_id) as open_area
        ")->where('storage_application_status', 'approved')->first()->toArray();
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
