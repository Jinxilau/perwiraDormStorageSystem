<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.staff')]
#[Title('Staff - Storage Application')]
class StorageApplicationReject extends Component
{
    #[Computed()]
    public function rejectedStorageApplications() {
        return StorageApplication::with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'rejected')->paginate(10);
    }
    
    public function render()
    {
        return view('livewire.storage-application-reject');
    }
}
