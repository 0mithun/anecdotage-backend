<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
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
        $appUrl = config('app.client_url', config('app.url'));
        $fullUrl = '/anecdotes/'.$notifiable->slug;

        $fullUrl = url($appUrl.$fullUrl);
        return (new FacebookPosterPost($notifiable->title))
            ->withLink($fullUrl)
            // ->withLink("https://laravel.com")
            // ->withImage($notifiable->threadImagePath)
            //->withImage("http://142.93.11.128/uploads/threads/143.jpeg")
        ;

        if ($notifiable->image_path == '') {
           return $this->shareFacebook($notifiable->title, $fullUrl);
        } else {
            $image_path =$notifiable->thread_image_path;
            $extension = $this->getFileExtensionFromURl( $notifiable->thread_image_path );
            $fileName =  $notifiable->id .'_'. uniqid();
            $fullFileName = $fileName . '.' . $extension;
            $image_path = 'download/temp/threads/' . $fullFileName;

            $this->file_download_curl($image_path, $notifiable->thread_image_path);

            $image = Storage::disk('public')->path($image_path);
            if($image){
                return $this->shareFacebook($notifiable->title, $fullUrl ,$image);
            }
        }
    }


    public function shareFacebook($title, $fullUrl, $image = null){
        if($image != null){
             return (new FacebookPosterPost($title))
                ->withLink($fullUrl)
                ->withImage($image)
            ;
        }

         return (new FacebookPosterPost($title))
                ->withLink($fullUrl)
            ;
    }

     /**
     * @param string $url
     * @return string
     */
    function getFileExtensionFromURl(string $url ) {
        $file = new \finfo( FILEINFO_MIME );
        $type = strstr( $file->buffer( file_get_contents( $url ) ), ';', true ); //Returns something similar to  image/jpg
        $extension = explode( '/', $type )[1];

        return $extension;
    }


    /**
     * @param string $fullPath
     * @param string $full_image_link
     * @return mixed
     */

    public function file_download_curl(string $fullPath, string $full_image_link)
    {
        $parts = explode('/', storage_path('app/public/'.$fullPath));
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen(storage_path('app/public/'.$fullPath), 'wb');
        $ch = curl_init($full_image_link);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        fclose($fp);
    }
}
