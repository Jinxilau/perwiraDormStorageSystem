<?php

namespace App\Livewire;

use App\Models\Semester;
use App\Models\Setting as ModelsSetting;
use App\Models\StorageRoom;
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

    public function mount()
    {
        $this->storage_application_enabled = ModelsSetting::get('storage_application_enabled') ? true : false;
        $this->missing_report_enabled = ModelsSetting::get('missing_report_enabled') ? true : false;

        $this->semesters = Semester::orderBy('academic_year', 'asc')
            ->orderBy('semester_no', 'asc')
            ->get();


        $current = Semester::where('is_current', true)->first();
        $this->selectedSemesterId = $current ? $current->id : null;
    }

    public function save()
    {
        $this->validate([
            'selectedSemesterId' => 'required|exists:semesters,id',
        ]);

        ModelsSetting::set('storage_application_enabled', $this->storage_application_enabled);
        ModelsSetting::set('missing_report_enabled', $this->missing_report_enabled);

        Semester::query()->update(['is_current' => false]);
        Semester::where('id', $this->selectedSemesterId)->update(['is_current' => true]);

        $this->dispatch('show_toast', message: "Settings updated successfully!", type: "success");
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
