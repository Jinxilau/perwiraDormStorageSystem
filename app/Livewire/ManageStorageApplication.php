<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class ManageStorageApplication extends Component
{
    #[Url(except: '')]
    public $status = 'pending';

    public function render()
    {
        return view('livewire.manage-storage-application');
    }
}
