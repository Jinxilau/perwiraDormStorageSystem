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
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.manage-storage-application')
            ->layout($layout)
            ->title($title . ' - Storage Application');;
    }
}
