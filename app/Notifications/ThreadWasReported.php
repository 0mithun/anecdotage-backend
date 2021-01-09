<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

/**
 * Class ThreadWasReported
 * @package App\Notifications
 */
class ThreadWasReported extends Notification implements ShouldQueue
{
    use Queueable;

    protected  $thread;
    protected  $reason;
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
            'message' => $this->reason,
            'link' => $this->thread->path()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->reason,
            'link' => $this->thread->path()
        ]);
    }
}
