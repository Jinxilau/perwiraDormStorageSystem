<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MissingReportMadeNotification extends Notification
{
    use Queueable;

    public $url;
    public $report;
    public $student;
    /**
     * Create a new notification instance.
     */
    public function __construct($report)
    {
        $this->report = $report;
        $this->student = $report->storedItems[0]->storageApplication->applicant;
        $this->url = route('manage_missing', ['search' => $this->report->id]);
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
            ->subject('New Missing Report Submitted')
            ->greeting('Hello ' . ($notifiable->name ?? "User") . ',')
            ->line('A new missing report has been made by ' . ($this->student->name ?? "a user") . '.')
            ->action('View report', $this->url)
            ->line('Please review the report when convenient.');
    }

    public function toDatabase(object $notifiable)
    {
        return [
            'type' => 'warning',
            'title' => 'New Missing Report Made!',
            'message' => 'New missing report by ' . $this->student->name,
            'application_id' => $this->student->id,
            'url' => $this->url,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage([
            'title' => 'New Missing Report!',
            'message' => 'New missing report by ' . $this->student->name,
            'application_id' => $this->student->id,
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
