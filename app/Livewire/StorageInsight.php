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
    }

    public function updatedDateFrom()
    {
        $this->loadChartData();
    }

    public function updatedDateTo()
    {
        $this->loadChartData();
    }

    public function updatedSelectedRoom()
    {
        $this->loadChartData();
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
    }
    protected function applyFilters($query)
    {
        if ($this->selectedSemester !== 'all') {
            $query->where('semester_id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->selectedRoom !== 'all') {
            $query->where(function ($q) {
                $q->whereHas('locker', function ($lockerQuery) {
                    $lockerQuery->where('storage_room_id', $this->selectedRoom);
                })->orWhereHas('openArea', function ($openAreaQuery) {
                    $openAreaQuery->where('storage_room_id', $this->selectedRoom);
                });
            });
        }

        return $query;
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
            ->groupBy(
                'semesters.id',
                'semesters.academic_year',
                'semesters.semester_no'
            )
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Semester filter
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
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

        // Apply filters through storage_applications
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
                $q->whereHas('storageApplication.locker', function ($lockerQuery) {
                    $lockerQuery->where('storage_room_id', $this->selectedRoom);
                })->orWhereHas('storageApplication.openArea', function ($openAreaQuery) {
                    $openAreaQuery->where('storage_room_id', $this->selectedRoom);
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

        $results = $query->get();

        $this->conditionLabels = $results->pluck('item_condition')->map(fn($cond) => ucfirst($cond))->toArray();
        $this->conditionValues = $results->pluck('count')->toArray();
    }

    private function loadCheckInCheckOutData()
    {
        $query = DB::table('semesters')
            ->leftJoin(
                'scan_logs',
                'semesters.id',
                '=',
                'scan_logs.semester_id'
            )
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw("SUM(CASE WHEN scan_logs.event_type = 'check_in' THEN 1 ELSE 0 END) as check_in_count"),
                DB::raw("SUM(CASE WHEN scan_logs.event_type = 'check_out' THEN 1 ELSE 0 END) as check_out_count")
            )
            ->groupBy(
                'semesters.id',
                'semesters.academic_year',
                'semesters.semester_no'
            )
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        // Filters
        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        if ($this->dateFrom) {
            $query->whereDate('scan_logs.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('scan_logs.created_at', '<=', $this->dateTo);
        }

        $results = $query->get();

        $this->checkLabels = $results->pluck('semester_label')->toArray();
        $this->checkInData = $results->pluck('check_in_count')->toArray();
        $this->checkOutData = $results->pluck('check_out_count')->toArray();
    }

    private function loadLockerVsOpenSpaceData()
    {
        $query = DB::table('semesters')
            ->leftJoin(
                'storage_applications',
                'semesters.id',
                '=',
                'storage_applications.semester_id'
            )
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw('SUM(CASE WHEN storage_applications.locker_id IS NOT NULL THEN 1 ELSE 0 END) as locker_count'),
                DB::raw('SUM(CASE WHEN storage_applications.open_area_id IS NOT NULL THEN 1 ELSE 0 END) as open_space_count')
            )
            ->groupBy(
                'semesters.id',
                'semesters.academic_year',
                'semesters.semester_no'
            )
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
        }

        $results = $query->get();

        $this->usageLabels = $results->pluck('semester_label')->toArray();
        $this->lockerData = $results->pluck('locker_count')->toArray();
        $this->openSpaceData = $results->pluck('open_space_count')->toArray();
    }

    private function loadLockerOccupancyRates()
    {
        $query = DB::table('semesters')
            ->leftJoin(
                'storage_applications',
                'semesters.id',
                '=',
                'storage_applications.semester_id'
            )
            ->leftJoin(
                'lockers',
                'storage_applications.locker_id',
                '=',
                'lockers.id'
            )
            ->select(
                DB::raw("CONCAT(semesters.academic_year, ' S', semesters.semester_no) as semester_label"),
                DB::raw('COUNT(DISTINCT lockers.id) as occupied')
            )
            ->groupBy(
                'semesters.id',
                'semesters.academic_year',
                'semesters.semester_no'
            )
            ->orderBy('semesters.academic_year')
            ->orderBy('semesters.semester_no');

        if ($this->selectedSemester !== 'all') {
            $query->where('semesters.id', $this->selectedSemester);
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
        $unclaimedItems = DB::table('storage_applications')
            ->join('semesters as app_sem', 'storage_applications.semester_id', '=', 'app_sem.id')
            ->join('semesters as next_sem', function ($join) {
                $join->on('next_sem.academic_year', '=', 'app_sem.academic_year')
                    ->whereRaw('next_sem.semester_no = app_sem.semester_no + 1');
            })
            ->join('q_r_codes', 'q_r_codes.storage_application_id', '=', 'storage_applications.id')
            ->where('q_r_codes.status', '!=', 'checked_out')
            ->whereDate(
                DB::raw('DATE_ADD(next_sem.start_date, INTERVAL 7 DAY)'),
                '<',
                Carbon::now()
            )
            ->count();

        $this->metrics = [
            'totalApplications' => StorageApplication::count(),

            'activeItems' => StorageApplication::where('storage_application_status', 'checked_in')->count(),

            'lockerOccupancyRate' => round(
                (Locker::where('status', 'occupied')->count() / max(Locker::count(), 1)) * 100,
                1
            ),

            'unclaimedItems' => $unclaimedItems,
        ];
    }

    public function exportExcel()
    {
        return Excel::download(new StorageReportExport, 'storage-report.xlsx');
    }

    public function exportPdf()
    {
        $data = [
            'metrics' => $this->metrics,
            'storageDemand' => $this->storageDemandValues,
        ];

        $pdf = Pdf::loadView('reports.dashboard-pdf', $data);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'dashboard-report.pdf'
        );
    }

    public function render()
    {
        return view('livewire.storage-insight');
    }
}
