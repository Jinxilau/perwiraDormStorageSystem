<?php

namespace App\Livewire;

use App\Models\Semester;
use App\Models\Setting as ModelsSetting;
use App\Models\StorageRoom;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Settings')]
#[Lazy()]
class Setting extends Component
{
    public $storage_application_enabled;
    public $missing_report_enabled;

    public $semesters;
    public $selectedSemesterId;

    // NEW: Properties for the "Create Semester" Form
    public $new_academic_year;
    public $new_semester_no;
    public $new_start_date;
    public $new_end_date;

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->storage_application_enabled = ModelsSetting::get('storage_application_enabled') ? true : false;
        $this->missing_report_enabled = ModelsSetting::get('missing_report_enabled') ? true : false;

        $this->semesters = Semester::orderBy('academic_year', 'desc') // Usually better to show newest first
            ->orderBy('semester_no', 'desc')
            ->get();

        // Only set selected ID if it hasn't been set yet (preserves selection during refreshes)
        if (!$this->selectedSemesterId) {
            $current = Semester::where('is_current', true)->first();
            $this->selectedSemesterId = $current ? $current->id : null;
        }
    }

    public function createSemester()
    {
        // 1. Validate
        $this->validate([
            'new_academic_year' => 'required|string|max:20', // e.g. "2024/2025"
            'new_semester_no'   => 'required|integer|in:1,2,3',
            'new_start_date'    => 'required|date',
            'new_end_date'      => 'required|date|after:new_start_date',
        ]);

        // 2. Create
        $semester = Semester::create([
            'academic_year' => $this->new_academic_year,
            'semester_no'   => $this->new_semester_no,
            'start_date'    => $this->new_start_date,
            'end_date'      => $this->new_end_date,
            'is_current'    => false, // Default to false, let them select it manually to be safe
        ]);

        // 3. Reset Form Fields
        $this->reset(['new_academic_year', 'new_semester_no', 'new_start_date', 'new_end_date']);

        // 4. Refresh List & Notify
        $this->refreshData(); // Refresh the dropdown list

        $this->dispatch('close-modal'); // Triggers JS to close modal
        $this->dispatch('show_toast', message: "New semester created successfully!", type: "success");
    }

    public function deleteSemester()
    {
        // 1. Validation
        if (!$this->selectedSemesterId) {
            return;
        }

        $semester = Semester::find($this->selectedSemesterId);

        if (!$semester) {
            $this->dispatch('show_toast', message: "Semester not found.", type: "error");
            return;
        }

        // 2. Prevent deleting the currently active semester (Optional safety check)
        if ($semester->is_current) {
            $this->dispatch('show_toast', message: "Cannot delete the currently active semester.", type: "error");
            return;
        }

        // 3. Attempt Delete
        try {
            $semester->delete();

            // 4. Reset selection and refresh list
            $this->selectedSemesterId = null;
            $this->refreshData(); // Reloads the $semesters list

            $this->dispatch('show_toast', message: "Semester deleted successfully.", type: "success");
        } catch (\Illuminate\Database\QueryException $e) {
            // 5. Handle Foreign Key Constraint (if semester has data linked to it)
            if ($e->getCode() == "23000") {
                $this->dispatch('show_toast', message: "Cannot delete: This semester has existing student applications linked to it.", type: "error");
            } else {
                $this->dispatch('show_toast', message: "An error occurred while deleting.", type: "error");
            }
        }
    }

    public function save()
    {
        $this->validate([
            'selectedSemesterId' => 'required|exists:semesters,id',
        ]);

        DB::transaction(function () {
            // 1. Save standard settings
            ModelsSetting::set('storage_application_enabled', $this->storage_application_enabled);
            ModelsSetting::set('missing_report_enabled', $this->missing_report_enabled);

            // 2. Update Semester Status
            // CRITICAL: You MUST set all to NULL first. 
            // If you try to set one to '1' while another is already '1', MySQL will throw a duplicate error.
            Semester::query()->update(['is_current' => null]);

            // 3. Set the selected semester to 1
            Semester::where('id', $this->selectedSemesterId)->update(['is_current' => 1]);
        });
        
        $this->dispatch('show_toast', message: "Settings updated successfully!", type: "success");
        $this->refreshData();
    }

    #[Computed()]
    public function selectedSemester()
    {
        return $this->semesters->firstWhere('id', $this->selectedSemesterId);
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
