<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.staff')]
#[Title('Staff - Storage Application')]
class StorageApplicationActive extends Component
{
    use WithPagination;


    
    #[Computed()]
    public function approvedStorageApplication() {
        return StorageApplication::with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'approved')->paginate(10);
    }

    public function render()
    {
        return view('livewire.storage-application-active');
    }
}
