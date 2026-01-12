<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissingReportStatusNotification extends Notification
{
    use Queueable;

    public $report;
    public $newStatus;
    public $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($report, $newStatus)
    {
        $this->report = $report;
        $this->newStatus = $newStatus;

        // Student-facing route (change if needed)
        $this->url = route('my_report', $report->id);
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
            ->subject('Update on Your Missing Item Report')
            ->greeting('Hello ' . ($notifiable->name ?? 'Student') . ',')
            ->line('The status of your missing item report has been updated.')
            ->line('Current status: ' . ucfirst($this->newStatus))
            ->action('View Report', $this->url)
            ->line('If you have further information, please update the report.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'type' => 'message',
            'title' => 'Missing Report Status Updated',
            'message' => 'Your missing report status is now "' . ucfirst($this->newStatus) . '".',
            'report_id' => $this->report->id,
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'type' => 'message',
            'title' => 'Missing Report Status Updated',
            'message' => 'Your missing report status is now "' . ucfirst($this->newStatus) . '".',
            'report_id' => $this->report->id,
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
