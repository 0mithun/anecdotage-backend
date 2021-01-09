<?php

namespace App\Notifications;

use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ThreadReportAdminNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    protected  $thread;
    protected  $type;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, $type)
    {
        $this->thread = $thread;
        $this->type = $type;
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
