<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserBanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reason)
    {
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
        $user = auth()->user();
        return [
            'message' => $this->reason,
            'link' => url('/')
        ];
    }
    public function toBroadcast($notifiable)
    {
        $user = auth()->user();
        return new BroadcastMessage([
            'message' =>  $this->reason,
            'link' => url('/')
        ]);
    }
}
