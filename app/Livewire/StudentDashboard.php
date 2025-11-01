<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.student')]
#[Title('Dashboard')]
#[Lazy()]
class StudentDashboard extends Component
{
    protected $listeners = ['notificationReceived' => '$refresh'];

    public function markAsRead($notificationId)
    {
        $noti = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($noti) {
            $noti->markAsRead();
            $this->dispatch('show_toast', message: "Notification marked as read.", type: "success");
        } else {
            $this->dispatch('show_toast', message: "Notification not found.", type: 'fail');
        }
    }

    #[Computed()]
    public function notifications()
    {
        return Auth::user()->notifications()->latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.student-dashboard');
    }
}
