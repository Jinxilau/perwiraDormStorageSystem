<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy()]
#[Layout('components.layouts.student')]
#[Title('My Storage')]
class MyStorage extends Component
{
    #[Computed()]
    public function studentStorageApplications()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs'])->get();
    }

    public function render()
    {
        return view('livewire.my-storage');
    }
}
