<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MyReport extends Component
{
    #[Computed()]
    public function studentStorageApplication()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['qrCode.missingReports.storedItems'])->first();
    }

    public function render()
    {
        return view('livewire.my-report')->layout('layouts.student')->title('Student - My Report');
    }
}
