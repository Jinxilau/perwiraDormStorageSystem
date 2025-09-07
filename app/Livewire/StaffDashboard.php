<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.staff')]
#[Title('Staff - Dashboard')]
class StaffDashboard extends Component
{
    public function render()
    {
        return view('livewire.staff-dashboard');
    }
}
