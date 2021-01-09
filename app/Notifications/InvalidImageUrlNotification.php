<?php

namespace App\Notifications;

use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InvalidImageUrlNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $thread;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
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
            'message' => "The Image url isn't a valid image url. Please check & add new image",
            'link' => $this->thread->path()
        ];
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "The Image url isn't a valid image url. Please check & add new image",
            'link' => $this->thread->path()
        ]);
    }
}
