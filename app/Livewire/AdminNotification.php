<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminNotification extends Component
{
    
    
    #[Computed()]
    public function notifications()
    {
        return Auth::user()->notifications;
    }

    public function render()
    {
        return view('livewire.admin-notification');
    }
}
