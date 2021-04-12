<?php

namespace App\Jobs;

use Image;
use Goutte\Client;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\ImageDownloadComplete;
use App\Notifications\InvalidImageUrlNotification;

class DownloadThreadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image_url;
    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image_url, Thread $thread)
    {
        $this->image_url = $image_url;
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       if($this->checkIsValidImageUrl($this->image_url)){
            $extension = $this->getFileExtensionFromURl( $this->image_url );
            $fileName =  $this->thread->id .'_'. uniqid();
            $fullFileName = $fileName . '.' . $extension;
            $image_path = 'download/threads/' . $fullFileName;

            $pixel_color = $this->getImageColorAttribute($this->image_url);

            $this->file_download_curl($image_path, $this->image_url);

            $old_image_path = $this->thread->image_path;

            $data = [
                'image_path'    => $this->optimizeImage($image_path),
                'image_path_pixel_color'    => $pixel_color
            ];

            $this->saveInfo($data);

            if($old_image_path != ''){
                 Storage::disk('public')->delete($old_image_path);
            }

            $user = User::where('id', $this->thread->user_id)->first();
            $user->notify(new ImageDownloadComplete($this->thread));



       }else{
           //send notification to user
            $user = User::where('id', $this->thread->user_id)->first();
            $user->notify(new InvalidImageUrlNotification($this->thread));
       }
    }

    /**
     * @param string $url
     * @return bool
     */

    public function checkIsValidImageUrl(string $url){
        if (@GetImageSize($url)) {
            return true;
        }
        return false;
    }


    /**
     * @param string  $image_path
     * @return string
     */
    public function getImageColorAttribute(string $image_path)
    {
        if ($image_path != '') {
            $splitName = explode('.', $image_path);
            $extension = strtolower(array_pop($splitName));

            if ($extension == 'jpg') {
                $im = imagecreatefromjpeg($image_path);
            }
            if ($extension == 'jpeg') {
                $im = imagecreatefromjpeg($image_path);
            } else if ($extension == 'png') {
                $im = imagecreatefrompng($image_path);
            } else if ($extension == 'gif') {
                $im = imagecreatefromgif($image_path);
            }

            if (isset($im)) {
                $rgb = imagecolorat($im, 0, 0);
                $colors = imagecolorsforindex($im, $rgb);
                array_pop($colors);
                array_push($colors, 1);
                $rgbaString = join(', ', $colors);

                return $rgbaString;
            }
        }
        return '';
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
     * @param array $data
     * @return mixed
     */
    public function saveInfo( array $data)
    {
        $this->thread->update($data);
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


    public function optimizeImage($image_path){
        $pathToImage = storage_path(sprintf('app/public/%s',$image_path));
        $file_path = sprintf('public/%s',$image_path);
        $pathToOutput = storage_path(sprintf("app/public/%s", $file_path));

        $parts = explode('/', $pathToOutput);
        array_pop($parts);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $width = Image::make($pathToImage)->width();

        if($width>600){
            // Image::make($pathToImage)
            //     ->fit(600,360, function ($constraint){
            //         $constraint->aspectRatio();
            //     })->save($large = $pathToOutput);
            //                 ;
           Image::make($pathToImage)
           ->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($large = $pathToOutput);;
            Storage::disk('public')->delete($image_path);
        }else{
            Storage::disk('public')->copy($image_path , $file_path);
            Storage::disk('public')->delete($image_path);
        }

        return $file_path;
    }
}
