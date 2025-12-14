<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Layout('components.layouts.student')]
#[Title('Storage Application')]
#[Lazy()]
class StorageApplicationCreate extends Component
{
    public function render()
    {
        return view('livewire.storage-application-create');
    }
}
