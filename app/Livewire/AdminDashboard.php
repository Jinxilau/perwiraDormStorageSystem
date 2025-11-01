<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
#[Lazy()]
class AdminDashboard extends Component
{

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
