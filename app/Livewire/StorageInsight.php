<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\QRCode;
use App\Models\ScanLog;
use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

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
    public $labels = []; # Semester Labels
    public $values = []; # Application Counts

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
    public $checkInData = [];
    public $checkOutData = [];

    // Locker / Open Space Data
    public $lockerData = []; # Counts of locker usage
    public $openSpaceData = []; # Counts of open space usage

    // Locker Occupancy Rates
    public $totalLockers = 0;
    public $lockerOccupancyRates = [];

    // Available options for filters
    public $semesters = [];
    public $rooms = [];

    public function mount()
    {
        $this->loadFilterOptions();
        $this->loadChartData();


        // $this->totalLockers = Locker::count();

        // $this->loadSemesterDemand();
        // $this->loadItemSizeBreakdown();
        // $this->loadItemTypeBreakdown();
        // $this->loadItemConditionBreakdown();
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
    public function loadSemesterDemand()
    {
        $data = Semester::withCount('storageApplications')
            ->orderBy('academic_year')
            ->orderBy('semester_no')
            ->get();

        $year = $data->pluck('academic_year');
        $sem = $data->pluck('semester_no');

        $this->labels = $year->zip($sem)->map(function ($item) {
            return $item[0] . ' - Sem ' . $item[1];
        })->toArray();
        $this->values = $data->pluck('storage_applications_count')->toArray();

        foreach ($data as $semester) {
            $this->checkInData[] = ScanLog::where('semester_id', $semester->id)
                ->where('event_type', 'check_in')
                ->count();

            $this->checkOutData[] = ScanLog::where('semester_id', $semester->id)
                ->where('event_type', 'check_out')
                ->count();

            // Locker usage
            $this->lockerData[] = StorageApplication::where('semester_id', $semester->id)
                ->where('storage_application_status', 'approved')
                ->whereNotNull('locker_id')
                ->count();

            // Open space usage
            $this->openSpaceData[] = StorageApplication::where('semester_id', $semester->id)
                ->where('storage_application_status', 'approved')
                ->whereNotNull('open_area_id')
                ->count();

            // Locker occupancy rate
            if ($this->totalLockers === 0) {
                $this->lockerOccupancyRates[] = 0;
                continue;
            }
            $occupiedLockers = QRCode::whereHas('storageApplication', function ($q) use ($semester) {
                $q->where('semester_id', $semester->id)
                    ->whereNotNull('locker_id');
            })
                ->where('status', 'checked_in')
                ->distinct('storage_application_id')
                ->count();

            $rate = round(($occupiedLockers / $this->totalLockers) * 100, 1);

            $this->lockerOccupancyRates[] = $rate;
        }
    }

    public function loadItemSizeBreakdown()
    {
        $sizes = StoredItem::select('estimated_size')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('estimated_size')
            ->get();

        $this->sizeLabels = $sizes->pluck('estimated_size')->toArray();
        $this->sizeValues = $sizes->pluck('total')->map(fn($v) => (int)$v)->toArray();
    }

    public function loadItemTypeBreakdown()
    {
        $types = StoredItem::select('item_type')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('item_type')
            ->get();

        $this->typeLabels = $types->pluck('item_type')->toArray();
        $this->typeValues = $types->pluck('total')->map(fn($t) => (int)$t)->toArray();
    }

    public function loadItemConditionBreakdown()
    {
        $data = StoredItem::selectRaw('item_condition, COUNT(*) as total')
            ->groupBy('item_condition')
            ->get();

        $this->conditionLabels = $data->pluck('item_condition')->toArray();
        $this->conditionValues = $data->pluck('total')->toArray();
    }


    public function render()
    {
        return view('livewire.storage-insight');
    }
}
