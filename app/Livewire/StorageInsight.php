<?php

namespace App\Livewire;

use App\Models\Semester;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Settings')]
#[Lazy()]
class StorageInsight extends Component
{
    // Application Semester Breakdown
    public $labels = [];
    public $values = [];

    // Size Breakdown
    public $sizeLabels = [];
    public $sizeValues = [];

    // Item Type Breakdown
    public array $typeLabels = [];
    public array $typeValues = [];

    public function mount()
    {
        $this->loadSemesterDemand();
        $this->loadItemSizeBreakdown();
        $this->loadItemTypeBreakdown();
    }

    public function loadSemesterDemand()
    {
        $data = Semester::withCount('storageApplications')->get();

        $year = $data->pluck('academic_year');
        $sem = $data->pluck('semester_no');

        $this->labels = $year->zip($sem)->map(function ($item) {
            return $item[0] . ' - Sem ' . $item[1];
        })->toArray();
        $this->values = $data->pluck('storage_applications_count')->toArray();
    }
    // public function loadSemesterData()
    // {
    //     $data = StorageApplication::with('semester')
    //         ->selectRaw('semester_id, COUNT(*) as total')
    //         ->groupBy('semester_id')
    //         ->get();

    //     $year = $data->pluck('semester.academic_year');
    //     $sem = $data->pluck('semester.semester_no');
    //     $this->labels = $year->zip($sem)->map(function ($item) {
    //         return $item[0] . ' - Sem ' . $item[1];
    //     })->toArray();
    //     $this->values = $data->pluck('total');
    // }

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


    public function render()
    {
        return view('livewire.storage-insight');
    }
}
