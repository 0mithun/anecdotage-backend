<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
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
           return $this->shareTwitter($status);
        } else {
            $image_path =$notifiable->thread_image_path;
            $extension = $this->getFileExtensionFromURl( $notifiable->thread_image_path );
            $fileName =  $notifiable->id .'_'. uniqid();
            $fullFileName = $fileName . '.' . $extension;
            $image_path = 'download/temp/threads/' . $fullFileName;

            $this->file_download_curl($image_path, $notifiable->thread_image_path);

            $image = Storage::disk('public')->path($image_path);
            if($image){
                return $this->shareTwitter($status, $image);
            }
        }
    }

    public function shareTwitter($status, $image = null){
        if($image != null){
            return (new TwitterStatusUpdate($status))
                    ->withImage($image )
                    ;
        }
        return (new TwitterStatusUpdate($status));
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
