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

    public $selectedApplications = [];
    public $selectAll = false;
    
    #[Computed()]
    public function approvedStorageApplications() {
        return StorageApplication::with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'approved')->paginate(10);
    }

    public function render()
    {
        return view('livewire.storage-application-active');
    }
}
