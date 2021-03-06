<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ThreadReportUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thread;
    protected $reason;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($thread, $reason)
    {
        $this->thread = $thread;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */


    public function toArray($notifiable)
    {

        return [
            'type' => $this->type,
            'thread' => $this->thread
        ];
    }

    public function toBroadcast($notifiable)
    {

        return new BroadcastMessage([
            'type' => $this->type,
            'thread' => $this->thread
        ]);
    }
}
