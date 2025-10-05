<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyStorage extends Component
{
    #[Computed()]
    public function studentStorageApplications()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs'])->get();
    }

    public function render()
    {
        return view('livewire.my-storage')->layout('layouts.student')->title('Student - My Storage');
    }
}
