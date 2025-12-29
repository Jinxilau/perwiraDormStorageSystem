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
    #[Computed]
    public function storageApplication()
    {
        return StorageApplication::query()
            ->where('applicant_id', Auth::id())
            ->with([
                'semester',
                'approver',
                'openArea',
                'locker',
                'storedItems.missingReport',
            ])
            ->first();
    }
    
    #[Computed]
    public function reports()
    {
        if (! $this->storageApplication) {
            return collect();
        }

        return $this->storageApplication
            ->storedItems
            ->pluck('missingReport')
            ->filter()
            ->unique('id')
            ->values();
    }

    public function render()
    {
        return view('livewire.my-report');
    }
}
