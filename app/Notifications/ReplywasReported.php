<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ReplywasReported extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;
    protected $reason;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reply, $reason)
    {
        $this->reply = $reply;
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

    /

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "User " . $user->username .  " reported a reply by " . $this->reply->owner->username . ', because: ' . $this->reason,
            'link' => $this->reply->path()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "User " . $user->username .  " reported a reply by " . $this->reply->owner->username . ', because: ' . $this->reason,
            'link' => $this->reply->path()
        ]);
    }
}
