<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin - Dashboard')]
class AdminDashboard extends Component
{

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
