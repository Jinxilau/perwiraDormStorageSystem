<?php

namespace App\Livewire;

use App\Exports\StorageReportExport;
use App\Models\Locker;
use App\Models\QRCode;
use App\Models\ScanLog;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin')]
#[Title('Storage Insight')]
#[Lazy()]
class StorageInsight extends Component
{
    // Filter properties
    public $selectedSemester = 'all';
    public $dateFrom;
    public $dateTo;
    public $selectedRoom = 'all';

    // Application Semester Breakdown
    public $storageDemandLabels = []; # Semester Labels
    public $storageDemandValues = []; # Application Counts

    // Size Breakdown
    public $sizeLabels = []; # Size Labels
    public $sizeValues = []; # Size Counts

    // Item Type Breakdown
    public array $typeLabels = []; # Type Labels
    public array $typeValues = []; # Type Counts

    // Item Group Breakdown
    public $conditionLabels = []; # Condition Labels
    public $conditionValues = []; # Condition Counts

    // Check-in / Check-out Data
    public $checkLabels = [];
    public $checkInData = [];
    public $checkOutData = [];

    // Locker / Open Space Data
    public $usageLabels = [];
    public $lockerData = []; # Counts of locker usage
    public $openSpaceData = []; # Counts of open space usage

    // Locker Occupancy Rates
    public $occupancyLabels = [];
    public $lockerOccupancyRates = [];

    // Available options for filters
    public $semesters = [];
    public $rooms = [];

    // Key Metrics
    public $metrics = [];

    public function mount()
    {
        $this->loadFilterOptions();
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function updatedSelectedSemester()
    {
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function updatedDateFrom()
    {
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function updatedDateTo()
    {
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function updatedSelectedRoom()
    {
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function loadFilterOptions()
    {
        $this->semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_no', 'desc')
            ->get();

        $this->rooms = DB::table('storage_rooms')
            ->select('id', 'room_name')
            ->where('room_status', '!=', 'closed')
            ->get();
    }

    public function resetFilters()
    {
        $this->selectedSemester = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->selectedRoom = 'all';
        $this->loadChartData();
        $this->loadMetrics();
    }

    public function loadChartData()
    {
        $this->loadStorageDemandData();
        $this->loadItemSizeBreakdown();
        $this->loadItemTypeBreakdown();
        $this->loadItemConditionBreakdown();
        $this->loadCheckInCheckOutData();
        $this->loadLockerVsOpenSpaceData();
        $this->loadLockerOccupancyRates();
    }

    private function loadStorageDemandData()
    {
        $query = DB::table('semesters')
            ->leftJoin('storage_applications', 'semesters.id', '=', 'storage_applications.semester_id')
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw('COUNT(storage_applications.id) as total')
            )
            ->groupBy('semesters.id', 'semesters.academic_year', 'semesters.semester_no')
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('lockers')
                        ->whereColumn('lockers.id', 'storage_applications.locker_id')
                        ->where('lockers.storage_room_id', $this->selectedRoom);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('open_areas')
                        ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                        ->where('open_areas.storage_room_id', $this->selectedRoom);
                });
            });
        }

        $results = $query->get();

        $this->storageDemandLabels = $results->pluck('semester_label')->toArray();
        $this->storageDemandValues = $results->pluck('total')->toArray();
    }

    private function loadItemSizeBreakdown()
    {
        $query = StoredItem::query()
            ->join('storage_applications', 'stored_items.storage_application_id', '=', 'storage_applications.id')
            ->select('stored_items.estimated_size', DB::raw('COUNT(*) as count'))
            ->groupBy('stored_items.estimated_size');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('storage_applications.semester_id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('lockers')
                        ->whereColumn('lockers.id', 'storage_applications.locker_id')
                        ->where('lockers.storage_room_id', $this->selectedRoom);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('open_areas')
                        ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                        ->where('open_areas.storage_room_id', $this->selectedRoom);
                });
            });
        }

        $results = $query->get();

        $this->sizeLabels = $results->pluck('estimated_size')->map(fn($size) => ucfirst($size))->toArray();
        $this->sizeValues = $results->pluck('count')->toArray();
    }

    private function loadItemTypeBreakdown()
    {
        $query = StoredItem::query()
            ->join('storage_applications', 'stored_items.storage_application_id', '=', 'storage_applications.id')
            ->select('stored_items.item_type', DB::raw('COUNT(*) as count'))
            ->groupBy('stored_items.item_type');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('storage_applications.semester_id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('lockers')
                        ->whereColumn('lockers.id', 'storage_applications.locker_id')
                        ->where('lockers.storage_room_id', $this->selectedRoom);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('open_areas')
                        ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                        ->where('open_areas.storage_room_id', $this->selectedRoom);
                });
            });
        }

        $results = $query->get();

        $this->typeLabels = $results->pluck('item_type')->map(fn($type) => ucfirst(str_replace('_', ' ', $type)))->toArray();
        $this->typeValues = $results->pluck('count')->toArray();
    }

    private function loadItemConditionBreakdown()
    {
        $query = StoredItem::query()
            ->join('storage_applications', 'stored_items.storage_application_id', '=', 'storage_applications.id')
            ->select('stored_items.item_condition', DB::raw('COUNT(*) as count'))
            ->groupBy('stored_items.item_condition');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('storage_applications.semester_id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('lockers')
                        ->whereColumn('lockers.id', 'storage_applications.locker_id')
                        ->where('lockers.storage_room_id', $this->selectedRoom);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('open_areas')
                        ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                        ->where('open_areas.storage_room_id', $this->selectedRoom);
                });
            });
        }

        $results = $query->get();

        $this->conditionLabels = $results->pluck('item_condition')->map(fn($cond) => ucfirst($cond))->toArray();
        $this->conditionValues = $results->pluck('count')->toArray();
    }

    private function loadCheckInCheckOutData()
    {
        $query = DB::table('semesters')
            ->leftJoin('scan_logs', 'semesters.id', '=', 'scan_logs.semester_id')
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw("SUM(CASE WHEN scan_logs.event_type = 'check_in' THEN 1 ELSE 0 END) as check_in_count"),
                DB::raw("SUM(CASE WHEN scan_logs.event_type = 'check_out' THEN 1 ELSE 0 END) as check_out_count")
            )
            ->groupBy('semesters.id', 'semesters.academic_year', 'semesters.semester_no')
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('scan_logs.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('scan_logs.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('storage_applications')
                    ->whereColumn('storage_applications.semester_id', 'scan_logs.semester_id')
                    ->where(function ($q) {
                        $q->whereExists(function ($lockerQuery) {
                            $lockerQuery->select(DB::raw(1))
                                ->from('lockers')
                                ->whereColumn('lockers.id', 'storage_applications.locker_id')
                                ->where('lockers.storage_room_id', $this->selectedRoom);
                        })->orWhereExists(function ($openQuery) {
                            $openQuery->select(DB::raw(1))
                                ->from('open_areas')
                                ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                                ->where('open_areas.storage_room_id', $this->selectedRoom);
                        });
                    });
            });
        }

        $results = $query->get();

        $this->checkLabels = $results->pluck('semester_label')->toArray();
        $this->checkInData = $results->pluck('check_in_count')->toArray();
        $this->checkOutData = $results->pluck('check_out_count')->toArray();
    }

    private function loadLockerVsOpenSpaceData()
    {
        $query = DB::table('semesters')
            ->leftJoin('storage_applications', 'semesters.id', '=', 'storage_applications.semester_id')
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw('SUM(CASE WHEN storage_applications.locker_id IS NOT NULL THEN 1 ELSE 0 END) as locker_count'),
                DB::raw('SUM(CASE WHEN storage_applications.open_area_id IS NOT NULL THEN 1 ELSE 0 END) as open_space_count')
            )
            ->groupBy('semesters.id', 'semesters.academic_year', 'semesters.semester_no')
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('lockers')
                        ->whereColumn('lockers.id', 'storage_applications.locker_id')
                        ->where('lockers.storage_room_id', $this->selectedRoom);
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('open_areas')
                        ->whereColumn('open_areas.id', 'storage_applications.open_area_id')
                        ->where('open_areas.storage_room_id', $this->selectedRoom);
                });
            });
        }

        $results = $query->get();

        $this->usageLabels = $results->pluck('semester_label')->toArray();
        $this->lockerData = $results->pluck('locker_count')->toArray();
        $this->openSpaceData = $results->pluck('open_space_count')->toArray();
    }

    private function loadLockerOccupancyRates()
    {
        $query = DB::table('semesters')
            ->leftJoin('storage_applications', 'semesters.id', '=', 'storage_applications.semester_id')
            ->leftJoin('lockers', 'storage_applications.locker_id', '=', 'lockers.id')
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw('COUNT(DISTINCT lockers.id) as occupied')
            )
            ->groupBy('semesters.id', 'semesters.academic_year', 'semesters.semester_no')
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Apply filters
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('storage_applications.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('storage_applications.created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where('lockers.storage_room_id', $this->selectedRoom);
        }

        $results = $query->get();

        $this->occupancyLabels = $results->pluck('semester_label')->toArray();

        // Total lockers (capacity)
        $totalLockersQuery = DB::table('lockers')
            ->where('status', '!=', 'under_maintenance');

        if ($this->selectedRoom !== 'all') {
            $totalLockersQuery->where('storage_room_id', $this->selectedRoom);
        }

        $totalLockers = $totalLockersQuery->count();

        $this->lockerOccupancyRates = $results->map(function ($row) use ($totalLockers) {
            if ($totalLockers > 0) {
                return round(($row->occupied / $totalLockers) * 100, 2);
            }
            return 0;
        })->toArray();
    }

    private function loadMetrics()
    {
        // Base query for applications
        $applicationsQuery = StorageApplication::query();
        
        // Apply filters to metrics
        if ($this->selectedSemester !== 'all') {
            $applicationsQuery->where('semester_id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $applicationsQuery->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $applicationsQuery->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $applicationsQuery->where(function ($q) {
                $q->whereHas('locker', function ($lockerQuery) {
                    $lockerQuery->where('storage_room_id', $this->selectedRoom);
                })->orWhereHas('openArea', function ($openAreaQuery) {
                    $openAreaQuery->where('storage_room_id', $this->selectedRoom);
                });
            });
        }

        $totalApplications = (clone $applicationsQuery)->count();
        $activeItems = (clone $applicationsQuery)->where('storage_application_status', 'checked_in')->count();
        $unclaimedCount = (clone $applicationsQuery)->unclaimed()->count();

        // Locker occupancy calculation
        $lockerQuery = Locker::where('status', 'occupied');
        $totalLockerQuery = Locker::query();

        if ($this->selectedRoom !== 'all') {
            $lockerQuery->where('storage_room_id', $this->selectedRoom);
            $totalLockerQuery->where('storage_room_id', $this->selectedRoom);
        }

        $occupiedLockers = $lockerQuery->count();
        $totalLockers = $totalLockerQuery->count();

        $this->metrics = [
            'totalApplications' => $totalApplications,
            'activeItems' => $activeItems,
            'lockerOccupancyRate' => $totalLockers > 0 ? round(($occupiedLockers / $totalLockers) * 100, 1) : 0,
            'unclaimedItems' => $unclaimedCount,
        ];
    }

    public function exportExcel()
    {
        return Excel::download(new StorageReportExport, 'storage-report.xlsx');
    }

    public function exportPdf()
    {
        if (class_exists('\Debugbar')) {
            debugbar()->disable();
        }

        $data = [
            'metrics' => $this->metrics,
            'storageDemand' => [
                'labels' => $this->storageDemandLabels,
                'values' => $this->storageDemandValues,
            ],
            'itemSizes' => [
                'labels' => $this->sizeLabels,
                'values' => $this->sizeValues,
            ],
            'itemTypes' => [
                'labels' => $this->typeLabels,
                'values' => $this->typeValues,
            ],
            'itemConditions' => [
                'labels' => $this->conditionLabels,
                'values' => $this->conditionValues,
            ],
            'checkActivity' => [
                'labels' => $this->checkLabels,
                'checkIn' => $this->checkInData,
                'checkOut' => $this->checkOutData,
            ],
            'lockerVsOpenSpace' => [
                'labels' => $this->usageLabels,
                'locker' => $this->lockerData,
                'openSpace' => $this->openSpaceData,
            ],
            'occupancy' => [
                'labels' => $this->occupancyLabels,
                'rates' => $this->lockerOccupancyRates,
            ],
        ];

        $pdf = Pdf::loadView('reports.dashboard-pdf', $data)
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            'storage-insight-report-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.storage-insight');
    }
}