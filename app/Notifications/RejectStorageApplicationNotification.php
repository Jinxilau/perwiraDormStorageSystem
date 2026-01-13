<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectStorageApplicationNotification extends Notification
{
    use Queueable;

    public $url;
    public $application;
    public $rejectionNote;

    /**
     * Create a new notification instance.
     */
    public function __construct($application, $rejectionNote)
    {
        $this->application = $application;
        $this->rejectionNote = $rejectionNote;
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
            ->subject('Update on Your Storage Application') // Neutral but clear subject
            ->greeting('Hello ' . ($notifiable->name ?? "Student") . ',')
            ->line('We regret to inform you that your storage application has been rejected.')
            ->line('Reason for rejection:')
            ->line($this->rejectionNote ? '"' . $this->rejectionNote . '"' : 'No specific reason provided.')
            ->action('View Application Status', $this->url)
            ->line('If you have questions or would like to re-apply, please check your dashboard.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'type' => 'warning',
            'title' => 'Your Storage Application Was Rejected',
            'message' => 'Your application has been rejected. Reason: ' . ($this->rejectionNote ?? 'N/A'),
            'application_id' => $this->application->id,
            'url' => $this->url,
        ];
    }
    
    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'type' => 'warning',
            'title' => 'Your Storage Application Was Rejected',
            'message' => 'Your application has been rejected. Check your dashboard for details.',
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
