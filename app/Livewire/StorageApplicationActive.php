<?php

namespace App\Livewire;

use App\Models\QRCode as ModelsQRCode;
use App\Models\ScanLog;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\StoredItem;
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
    public $semester = '';
    #[Url(except: '')]
    public $storageType = '';
    #[Url]
    public int $unclaimedOnly = 0;
    #[Url(except: '')]
    public $qrStatus = '';
    #[Url(except: '')]
    public $sortCheckIn = '';
    #[Url(except: '')]
    public $sortCheckOut = '';
    #[Url(except: '')]
    public $sortDate = 'latest';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public $modalData;

    protected function resetsPage()
    {
        return ['search', 'room', 'semester', 'storageType', 'qrStatus'];
    }

    public function updatedPaginationInt()
    {
        $this->resetPage();
    }

    // Reset other sorts when Check-In sort is changed
    public function updatedSortCheckIn($value)
    {
        if ($value) {
            $this->reset(['sortCheckOut', 'sortName', 'sortDate']);
        }
    }

    // Reset other sorts when Check-Out sort is changed
    public function updatedSortCheckOut($value)
    {
        if ($value) {
            $this->reset(['sortCheckIn', 'sortName', 'sortDate']);
        }
    }

    // Reset other sorts when Applicant Name sort is changed
    public function updatedSortName($value)
    {
        if ($value) {
            $this->reset(['sortCheckIn', 'sortCheckOut', 'sortDate']);
        }
    }

    // Reset other sorts when Application Date sort is changed
    public function updatedSortDate($value)
    {
        if ($value) {
            $this->reset(['sortCheckIn', 'sortCheckOut', 'sortName']);
        }
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
        $this->reset([
            'search',
            'room',
            'semester',
            'storageType',
            'unclaimedOnly',
            'qrStatus',
            'sortCheckIn',
            'sortCheckOut',
            'sortDate',
            'sortName',
            'paginationInt'
        ]);

        // Crucial: Reset pagination to page 1
        $this->resetPage();

        $this->dispatch('show_toast', message: 'Filters cleared.', type: 'success');
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

    // public function checkout(StorageApplication $application)
    // {
    //     $application->load(['qrCode', 'locker', 'openArea']);

    //     if (!$application->qrCode) {
    //         $this->dispatch('show_toast', message: "Error: No QR Code found.", type: "fail");
    //         return;
    //     }

    //     try {
    //         DB::transaction(function () use ($application) {
    //             $qr = ModelsQRCode::where('id', $application->qrCode->id)
    //                 ->lockForUpdate()
    //                 ->first();

    //             // 1. Logic Checks (Order Matters!)
    //             if ($qr->status === 'checked_out') {
    //                 throw new \Exception("This storage has already been checked out.");
    //             }

    //             if ($qr->status !== 'checked_in') {
    //                 // If it's 'pending' or 'new', they can't check out yet.
    //                 throw new \Exception("Item must be Checked In before it can be Checked Out.");
    //             }

    //             // 2. Create Scan Log
    //             ScanLog::create([
    //                 'qr_code_id' => $qr->id,
    //                 'event_type' => 'check_out'
    //             ]);

    //             $qr->update(['status' => 'checked_out']);
    //             $qr->increment('scanned_count');

    //             if ($application->locker) {
    //                 $application->locker->update(['status' => 'available']);
    //             }
    //             if ($application->openArea) {
    //                 $application->openArea->update(['status' => 'available']);
    //             }
    //         });

    //         $this->dispatch('show_toast', message: "Check-out successful!", type: "success");
    //     } catch (\Exception $e) {
    //         Log::error("Checkout Error: " . $e->getMessage());
    //         $this->dispatch('show_toast', message: "System error during check-out.", type: "fail");
    //     }
    // }

    #[Computed()]
    public function approvedStorageApplications()
    {
        // 1. Prepare the subquery for Scan Logs (Aggregates logs once)
        $scanTimesSubquery = DB::table('scan_logs')
            ->select('qr_code_id')
            ->selectRaw("MAX(CASE WHEN event_type = 'check_in' THEN created_at END) as last_check_in")
            ->selectRaw("MAX(CASE WHEN event_type = 'check_out' THEN created_at END) as last_check_out")
            ->groupBy('qr_code_id');

        $query = StorageApplication::query()
            ->with(['applicant', 'approver', 'storedItems', 'openArea.storageRoom', 'locker.storageRoom', 'qrCode.scanLogs', 'semester'])
            ->where('storage_application_status', 'approved');

        // 2. Joins & Selection
        $query->select('storage_applications.*')
            ->leftJoin('q_r_codes as qr', 'qr.storage_application_id', '=', 'storage_applications.id')
            // This connects the aggregated scan times to your QR codes
            ->leftJoinSub($scanTimesSubquery, 'scan_times', 'scan_times.qr_code_id', '=', 'qr.id');

        // 3. Apply Filters
        if ($this->search) {
            $query->where(function ($q) {
                if (is_numeric($this->search)) {
                    // Specify table name to avoid ambiguous column error
                    $q->where('storage_applications.id', $this->search);
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
            // Specify table name just to be safe
            $query->where('storage_applications.semester_id', $this->semester);
        }

        if ($this->storageType) {
            if ($this->storageType === 'locker') {
                $query->whereNotNull('storage_applications.locker_id');
            } elseif ($this->storageType === 'openArea') {
                $query->whereNotNull('storage_applications.open_area_id');
            }
        }

        if ($this->unclaimedOnly === 1) {
            $query->unclaimed();
        }

        if ($this->qrStatus) {
            // Since we joined the table, we can filter directly on the column
            // instead of using whereHas, which is slightly more performant
            $query->where('qr.status', $this->qrStatus);
        }

        // 4. Apply Sorting
        if ($this->sortCheckIn) {
            $query->orderBy('scan_times.last_check_in', $this->sortCheckIn === 'latest' ? 'desc' : 'asc');
        } elseif ($this->sortCheckOut) {
            $query->orderBy('scan_times.last_check_out', $this->sortCheckOut === 'latest' ? 'desc' : 'asc');
        } elseif ($this->sortName) {
            $query->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'storage_applications.applicant_id'),
                $this->sortName === 'desc' ? 'desc' : 'asc'
            );
        } else {
            $direction = ($this->sortDate === 'oldest') ? 'asc' : 'desc';
            $query->orderBy('storage_applications.created_at', $direction);
        }

        return $query->paginate($this->paginationInt);
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
            'qrCode.scanLogs',
        ])->find($this->modalData);
    }

    // Metrics for dashboard cards
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
    public function approvedThisSemester()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        return StorageApplication::where('storage_application_status', 'approved')
            ->where('semester_id', $currentSemesterId)
            ->count();
    }
    #[Computed()]
    public function pendingCheckinCount()
    {
        return StorageApplication::query()
            ->where('storage_application_status', 'approved')
            ->whereHas('semester', fn($q) => $q->where('is_current', true))
            ->whereHas('qrCode', fn($q) => $q->where('status', 'not_scanned'))
            ->count();
    }
    #[Computed()]
    public function pendingCheckoutCount()
    {
        return StorageApplication::query()
            ->where('storage_application_status', 'approved')
            ->whereHas('semester', fn($q) => $q->where('is_current', true))
            // "Pending checkout" implies they are currently checked in
            ->whereHas('qrCode', fn($q) => $q->where('status', 'checked_in'))
            ->count();
    }
    #[Computed()]
    public function totalItemsCount()
    {
        // This query is slightly different as it starts from StoredItem
        return StoredItem::whereHas('storageApplication', function ($q) {
            $q->where('storage_application_status', 'approved')
                ->whereHas('semester', fn($sq) => $sq->where('is_current', true));
        })->count();
    }
    #[Computed()]
    public function unclaimedCount()
    {
        return StorageApplication::unclaimed()->count();
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
        return view('livewire.storage-application-active');
    }
}
