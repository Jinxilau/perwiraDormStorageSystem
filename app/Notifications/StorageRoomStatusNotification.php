<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StorageRoomStatusNotification extends Notification
{
    use Queueable;

    public $room;
    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($room)
    {
        $this->room = $room;
        $this->status = $room->room_status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Storage Room Availability Update')
            ->greeting('Hello ' . ($notifiable->name ?? 'Student') . ',')
            ->line('The storage room "' . $this->room->room_name . '" status has been updated.')
            ->line(
                $this->status === 'open'
                    ? 'The storage room is now OPEN and available for check-in.'
                    : 'The storage room is now CLOSED and temporarily unavailable.'
            )
            ->line('Thank you for your attention.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'type' => $this->status === 'open' ? 'announcement' : 'warning',
            'title' => $this->status === 'open' ? 'Storage Room Opened' : 'Storage Room Closed',
            'message' => $this->status === 'open' ? 'Storage room "' . $this->room->room_name . '" is now available for check-in.' : 'Storage room "' . $this->room->room_name . '" is currently closed.',
            'room_id' => $this->room->id,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->status === 'open' ? 'Storage Room Opened' : 'Storage Room Closed',
            'message' => $this->status === 'open' ? 'Storage room "' . $this->room->room_name . '" is now OPEN for check-in.' : 'Storage room "' . $this->room->room_name . '" is currently CLOSED.',
            'room_id' => $this->room->id,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
