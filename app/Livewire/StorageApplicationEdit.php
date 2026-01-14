<?php

namespace App\Livewire;

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
class StorageApplicationEdit extends Component
{
    public StorageApplication $application;

    #[Computed()]
    public function isApplicationEnabled()
    {
        return Setting::get('storage_application_enabled', true);
    }


    public function mount(StorageApplication $application) // Route Model Binding
    {
        if ($application->applicant_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Optional: prevent editing non-pending application
        if ($application->storage_application_status !== 'pending') {
            abort(403, 'You cannot edit this application anymore.');
        }

        $this->application = $application;
    }

    public function render()
    {
        return view('livewire.storage-application-edit');
    }
}
