<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.staff')]
#[Title('Notifications')]
#[Lazy()]
class StaffNotification extends Component
{
    use WithPagination;

    // 1. Add Filter Properties
    public $filterStatus = 'all'; // Options: all, unread, read
    public $filterType = 'all';   // Options: all, general, success, warning

    protected $listeners = ['notificationReceived' => '$refresh'];

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function markAllAsRead()
    {
        // Note: This usually targets all unread, regardless of current filter
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
        // Start the query
        $query = Auth::user()->notifications();

        // 3. Apply Status Filter
        if ($this->filterStatus === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filterStatus === 'read') {
            $query->whereNotNull('read_at');
        }

        // 4. Apply Type Filter (Assuming JSON 'data' column)
        if ($this->filterType !== 'all') {
            $query->where('data->type', $this->filterType);
        }

        return $query->paginate(5);
    }


    public function render()
    {
        return view('livewire.staff-notification');
    }
}
