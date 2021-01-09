<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ThreadWasUpdated extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * The thread that was updated.
     *
     * @var \App\Thread
     */
    protected $thread;

    /**
     * The new reply.
     *
     * @var \App\Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param \App\Thread $thread
     * @param \App\Reply  $reply
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->reply->owner->name . ' replied to ' . $this->thread->title,
            'link' => $this->reply->path()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->reply->owner->name . ' replied to ' . $this->thread->title,
            'link' => $this->reply->path()
        ]);
    }
}
