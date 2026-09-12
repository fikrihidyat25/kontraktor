<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProyekBaruNotification extends Notification
{
    use Queueable;

    protected $proyek;

    /**
     * Create a new notification instance.
     */
    public function __construct($proyek)
    {
        $this->proyek = $proyek;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'proyek_id' => $this->proyek->id,
            'title' => 'Proyek Baru Ditugaskan',
            'message' => 'Anda telah ditugaskan pada proyek: ' . $this->proyek->nama_proyek,
            'url' => url('/dashboard'),
        ];
    }
}