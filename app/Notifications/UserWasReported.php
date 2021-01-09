<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserWasReported extends Notification implements ShouldQueue
{
    use Queueable;

    protected  $reported_user;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reported_user, $reason)
    {
        $this->reported_user = $reported_user;
        $this->reason  = $reason;
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
            'message' => "User " . $user->username . " " . " reported user " . $this->reported_user->username,
            'link' => url('/threads?by=' . $this->reported_user->username)
        ];
    }
    public function toBroadcast($notifiable)
    {
        $user = auth()->user();
        return new BroadcastMessage([
            'message' => "User " . $user->username . " " . " reported user " . $this->reported_user->username,
            'link' => url('/threads?by=' . $this->reported_user->username)
        ]);
    }
}
