<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\FacebookPoster\FacebookPosterPost;
use NotificationChannels\FacebookPoster\FacebookPosterChannel;

class ThreadPostFacebook extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FacebookPosterChannel::class];
    }

    public function toFacebookPoster($notifiable)
    {
        return (new FacebookPosterPost($notifiable->title))
            ->withLink(url($notifiable->path()))
            // ->withLink("https://laravel.com")
            // ->withImage($notifiable->threadImagePath)
            //->withImage("http://142.93.11.128/uploads/threads/143.jpeg")
        ;
    }
}
