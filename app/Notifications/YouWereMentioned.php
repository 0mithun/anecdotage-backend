<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class YouWereMentioned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param \App\Reply $reply
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->reply->owner->name . ' mentioned you in ' . $this->reply->thread->title,
            'link' => $this->reply->path()
        ];
    }


    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->reply->owner->name . ' mentioned you in ' . $this->reply->thread->title,
            'link' => $this->reply->path()
        ]);
    }
}
