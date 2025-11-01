<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.student')]
#[Title('My Missing Report')]
#[Lazy()]
class MyReport extends Component
{
    #[Computed()]
    public function studentStorageApplication()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['storedItems.missingReport'])->first();
    }

    public function render()
    {
        return view('livewire.my-report');
    }
}
