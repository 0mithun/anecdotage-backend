<?php

namespace App\Http\Controllers\Slide;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SlideResource;
use App\Repositories\Contracts\IThread;

class SlideController extends Controller
{
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function index(){
        $threads = $this->threads->withCriteria([

        ])
        // ->where('id','<',4)
        // ->where('slide_image_pos', '!=', "")
        ->whereNotNull('slide_image_pos')
        ->select([
            "id",
            "user_id" ,
            "channel_id",
            "title",
            "slug",
            "image_path",
            "image_path_pixel_color" ,
            "image_description",
            "age_restriction",
            "slide_body",
            "slide_color_0",
            "slide_color_1",
            "slide_color_2",
            "slide_color_bg",
            "slide_image_pos",
            "created_at",
            "updated_at",
        ])
        ->orderBy('updated_at', 'desc')
        // ->limit(10)
        // ->get()
        ->paginate(10)
        // ->toSql()
        ;

        return response()->json(['query'=> $threads]) ;
        return  SlideResource::collection($threads);
    }


}