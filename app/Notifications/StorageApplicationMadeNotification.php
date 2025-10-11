<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StorageApplicationMadeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Storage Application Submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new storage application has been made by ' . $this->application->user->name)
            ->action('View Application', route('manage_storage_pending'))
            ->line('Please review the application when convenient.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'message' => 'New storage application by ' . $this->application->applicant_id,
            'application_id' => $this->application->id
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'message' => 'New storage application by ' . $this->application->applicant_id,
            'application_id' => $this->application->id,
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
