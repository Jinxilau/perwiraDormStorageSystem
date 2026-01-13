<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApproveStorageApplicationNotification extends Notification
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
        $this->url = route('my_storage');
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
            ->subject('Your Storage Application Has Been Approved')
            ->greeting('Hello ' . ($notifiable->name ?? "Student") . ',')
            ->line('Good news! Your storage application has been approved by our staff.')
            ->line('You can now proceed to store your items as per your approved request.')
            ->action('View Application Details', $this->url)
            ->line('Please review the application details and follow any next steps if required.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'type' => 'success',
            'title' => 'Your Storage Application Has Been Approved!',
            'message' => 'Good news! Your storage application has been approved by our staff. You can now proceed to store your items as per your approved request.',
            'application_id' => $this->application->id,
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'title' => 'Your Storage Application Has Been Approved!',
            'message' => 'You can now proceed to store your items as per your approved request.',
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
