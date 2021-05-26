<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SlideCategoryResource;
use App\Http\Resources\SlideResource;
use App\Jobs\TakeSlideScreenshot;
use App\Models\SlideCategory;
use App\Models\Tag;
use App\Models\Thread;
use App\Repositories\Contracts\IThread;
use Symfony\Component\HttpFoundation\Response;

class SlideController extends Controller
{
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function getSingleSlide(Thread $thread){
        return  new SlideResource($thread);
    }

    public function update(Request $request, Thread $thread){
        $data = $request->only(['slide_body','slide_image_pos','slide_color_bg','slide_color_0','slide_color_1','slide_color_2']);
        $thread = $this->threads->update($thread->id, $data);

        return  response(['success'=> true, Response::HTTP_ACCEPTED]);
    }

    public function takeScreenshot(Request $request, Thread $thread){
        dispatch(new TakeSlideScreenshot($thread));
        return  new SlideResource($thread);
    }

}
