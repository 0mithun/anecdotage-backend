<?php

namespace App\Jobs;

use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Image;

class OptimizeThreadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thread;
    protected $image_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image_path, Thread $thread)
    {
        $this->image_path  = $image_path;
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pathToImage = storage_path(sprintf('app/public/%s',$this->image_path));
        $file_path = sprintf('uploads/%s',$this->image_path);
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
            // ;
            Image::make($pathToImage)
                ->fit(600)
                ->save($large = $pathToOutput)
            ;

            Storage::disk('public')->delete($this->image_path);
        }else{
            Storage::disk('public')->copy($this->image_path , $file_path);
            Storage::disk('public')->delete($this->image_path);
        }

        $this->thread->update([
            'image_path'    =>$file_path
        ]);
    }

}
