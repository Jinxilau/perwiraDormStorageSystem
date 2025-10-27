<?php

namespace App\Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDropdown extends Component
{
    protected $listeners = ['notificationReceived' => 'handleNotification'];
    public $braodcastNotificationId;

    public function handleNotification($notification = null)
    {
        if ($notification) {
            $this->braodcastNotificationId = $notification['id'];
            $this->dispatch('show_notification', notification: $notification);
        }
    }

    public function markAsRead()
    {
        $notification = Auth::user()->notifications()->where('id', $this->broadcastNotificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead($id)
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    #[Computed()]
    public function countUnread()
    {
        return Auth::user()->unreadNotifications->count();
    }

    #[Computed()]
    public function unreadNotifications()
    {
        return Auth::user()->unreadNotifications()->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.component.notification-dropdown');
    }
}
