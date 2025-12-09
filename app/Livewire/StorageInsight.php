<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Settings')]
#[Lazy()]
class StorageInsight extends Component
{


    public function render()
    {
        $data = StorageApplication::selectRaw('storage_application_status, count(*) as total')
            ->groupBy('storage_application_status')
            ->get();

        return view('livewire.storage-insight', [
            'labels' => $data->pluck('storage_application_status'),
            'values' => $data->pluck('total'),
        ]);
    }
}
