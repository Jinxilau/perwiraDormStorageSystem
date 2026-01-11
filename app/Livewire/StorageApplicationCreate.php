<?php

namespace App\Livewire;

use App\Models\Semester;
use App\Models\Setting;
use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Layout('components.layouts.student')]
#[Title('Storage Application')]
#[Lazy()]
class StorageApplicationCreate extends Component
{
    // 1. Check if application is enabled
    #[Computed]
    public function isApplicationEnabled()
    {
        return Setting::get('storage_application_enabled', true);
    }

    // 2. Get the current semester
    #[Computed()]
    public function currentSemester()
    {
        return Semester::where('is_current', true)->first();
    }

    // 3. Check if user already applied
    #[Computed]
    public function hasApplied()
    {
        // Guard clause: If no semester is set as current, they can't have applied to it
        if (! $this->currentSemester) {
            return false;
        }

        return StorageApplication::query()
            ->where('applicant_id', Auth::id())
            ->where('semester_id', $this->currentSemester->id)
            ->exists();
    }

    public function render()
    {
        return view('livewire.storage-application-create');
    }
}
