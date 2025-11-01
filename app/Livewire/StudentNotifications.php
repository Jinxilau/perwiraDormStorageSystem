<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.student')]
#[Title('Notifications')]
#[Lazy()]
class StudentNotifications extends Component
{
    use WithPagination;

    protected $listeners = ['notificationReceived' => '$refresh'];

    public function markAllAsRead()
    {
        $this->notifications->markAsRead();

        $this->dispatch('show_toast', message: "All notifications marked as read.", type: "success");
    }

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

    public function deleteNotification($notificationId)
    {
        $noti = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($noti) {
            $noti->delete();
            $this->dispatch('show_toast', message: "Notification deleted successfully.", type: "success");
        } else {
            $this->dispatch('show_toast', message: "Notification not found.", type: 'fail');
        }
    }

    #[Computed()]
    public function notifications()
    {
        return Auth::user()->notifications()->paginate(5);
    }


    public function render()
    {
        return view('livewire.student-notifications');
    }
}
