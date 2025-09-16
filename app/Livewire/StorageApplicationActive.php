<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StorageApplicationActive extends Component
{
    use WithPagination;

    public $selectedApplications = [];
    public $selectAll = false;
    public $application;

    #[On('view_modal')]
    public function viewApplicationModal($id) {
        $this->application = $this->approvedStorageApplications->where('id', $id)->first();
        $this->dispatch('display_modal');
    }

    public function checkin() {
        // TODO
    }

    public function checkout() {
        // TODO
    }
    
    #[Computed()]
    public function approvedStorageApplications()
    {
        return StorageApplication::with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom', 'qrCode'])->where('storage_application_status', 'approved')->paginate(10);
    }

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.storage-application-active')
            ->layout($layout)
            ->title($title . ' - Storage Application');
    }
}
