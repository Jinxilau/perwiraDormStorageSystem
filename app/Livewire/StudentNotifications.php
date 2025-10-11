<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StudentNotifications extends Component
{

    #[Computed()]
    public function notifications()
    {
        return Auth::user()->notifications;
    }

    public function render()
    {
        return view('livewire.student-notifications')->layout('layouts.student')->title('Student - Notifications');
    }
}
