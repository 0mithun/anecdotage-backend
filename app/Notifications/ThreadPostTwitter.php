<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Twitter\TwitterChannel;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class ThreadPostTwitter extends Notification implements ShouldQueue
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
        return [TwitterChannel::class];
    }



    public function toTwitter($notifiable)
    {
        $limit = 270;
        // $title_count = strlen($notifiable->title);
        $tag_count = strlen("#anecdotes");

        $appUrl = config('app.client_url', config('app.url'));
        $fullUrl = '/anecdotes/'.$notifiable->slug;

        $fullUrl = url($appUrl.$fullUrl);


        $path_count = strlen($fullUrl);
        $total_count = $path_count + $tag_count;
        $end = $limit - $total_count;

        $description = substr(strip_tags($notifiable->body), 0, $end);

        $status = "{$description} #anecdotes {$fullUrl} ";

        if ($notifiable->image_path == '') {
            // return (new TwitterStatusUpdate($notifiable->title));
            return (new TwitterStatusUpdate($status));
        } else {
            // return (new TwitterStatusUpdate($notifiable->title))

            if (preg_match("/http/i", $notifiable->image_path)) {
                $image_path = $notifiable->image_path;
            } else if (preg_match("/download/i", $notifiable->image_path)) {
                 $image_path =  asset($notifiable->image_path);
            }else{
                $image_path =  asset('storage/' . $notifiable->image_path);
            }



            dump($image_path);
            return (new TwitterStatusUpdate($status))
                // ->withImage($image_path)notifiable->
                ;
        }
    }
}
