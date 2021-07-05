<?php

namespace App\Jobs;

use App\Models\SlideSetting;
use Image;
use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TakeSlideScreenshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thread;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $slideSetting = SlideSetting::first();
        $options = [
            'width' => 1920,
            // 'height'=> 900,
            'quality' => 90,
            // 'zoomfactor'=> 1
          ];

        $conv = new \Anam\PhantomMagick\Converter();
        //  $conv->setBinary('/usr/local/bin/phantomjs');

        $conv->imageOptions($options);
        $path  = sprintf("screenshots/%s_%s.jpg",$this->thread->id, uniqid());
        // $pathToImage = storage_path('app/public/'.$path);
        $pathToImage = public_path($path);

        // $source =  env('SLIIDE_APP_URL').'/i-'.$this->thread->id;
        $source =   rtrim(env('SLIIDE_APP_URL'),'/').'/'.$this->thread->slug;
        // dump($source);
        $conv->source($source)


        // $conv->source('http://localhost:3000/i-120011')
            ->toJpg()
            ->save($pathToImage);

        // create empty canvas
        $imgColor = Image::canvas(100, 50);
        // $imgColor->fill('#181818');
        $imgColor->fill('#000000');

        $img = Image::make($pathToImage);
        $img->crop(1720, 780,50,0);
        $img->insert($imgColor, 'top-right', 0, 0);

        // $img->crop(1770, 750,0,70);

        // // use callback to define details
        // $img->text('anecdotage.com', 1400, 675, function($font) {
        //     $font->file(public_path('fonts/roboto/Roboto-Regular.ttf'));
        //     $font->size(50);
        //     $font->color(array(255, 255, 255, 0.5));
        //     $font->align('center');
        //     $font->valign('top');
        // });
        $img->save($pathToImage);


        Storage::disk('screenshots')->delete($this->thread->slide_screenshot);

        $this->thread->slide_screenshot  = $path;
        $this->thread->save();
    }
}
