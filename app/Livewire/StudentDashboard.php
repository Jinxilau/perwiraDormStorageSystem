<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.student')]
#[Title('Student - Dashboard')]
class StudentDashboard extends Component
{
    public function render()
    {
        return view('livewire.student-dashboard');
    }
}
