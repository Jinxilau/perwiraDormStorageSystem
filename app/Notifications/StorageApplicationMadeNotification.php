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

    public $url;
    public $application;
    /**
     * Create a new notification instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
        $this->url = route('manage_storage_application', ['status' => 'pending']);
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
            ->greeting('Hello ' . ($notifiable->name ?? "User") . ',')
            ->line('A new storage application has been made by ' . ($this->application->applicant->name ?? "a user") . '.')
            ->action('View Application', $this->url)
            ->line('Please review the application when convenient.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'type' => 'message',
            'title' => 'New Storage Application Made!',
            'message' => 'New storage application by ' . $this->application->applicant_id,
            'application_id' => $this->application->id,
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'type' => 'message',
            'title' => 'New Storage Application Made!',
            'message' => 'New storage application by ' . $this->application->applicant_id,
            'application_id' => $this->application->id,
            'url' => $this->url,
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
