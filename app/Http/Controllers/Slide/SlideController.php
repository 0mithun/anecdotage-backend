<?php

namespace App\Http\Controllers\Slide;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Models\SlideCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SlideResource;
use App\Repositories\Contracts\IThread;
use App\Http\Resources\SlideCategoryResource;
use Symfony\Component\HttpFoundation\Response;

class SlideController extends Controller
{
    protected $threads;

    protected $selectedFields = [
        "id",
        "user_id" ,
        "channel_id",
        "title",
        "slug",
        "image_path",
        "image_path_pixel_color" ,
        "image_description",
        "amazon_product_url",
        "age_restriction",
        "slide_body",
        "slide_color_0",
        "slide_color_1",
        "slide_color_2",
        "slide_color_bg",
        "slide_image_pos",
        "slide_ready",
        "created_at",
        "updated_at",
    ];

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function index(){
        $threads = $this->buildQuery()
        ->orderBy('updated_at', 'desc')
        // ->limit(10)
        // ->get()
        // ->paginate(1)
        // ->paginate((int) request('per_page', 10))
        ->paginate(10)
        // ->toSql()
        // ->get()
        ;

        // return response()->json(['query'=> $threads]) ;
        return  SlideResource::collection($threads);
    }

    public function show($id){
        $threads = $this->buildQuery()
            ->where('id',$id)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

            return  SlideResource::collection($threads);
    }


    public function report($id, Request $request){
        $request->validate([
            'note'  =>      ['required'],
            'source'  =>      ['required'],
            'email'  =>      ['email','required'],
        ]);

        $thread = $this->threads->find($id);

        if(!$thread){
            return response(['success' => false], Response::HTTP_NOT_FOUND);
        }


        DB::table('slide_reprorts')->insert([
            'note'  => $request->note,
            'source'  => $request->source,
            'email'  => $request->email,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response(['success' => true], Response::HTTP_ACCEPTED);


    }


    public function categories(){
        $categories = SlideCategory::orderBy('display_text','ASC')->get();

        return  SlideCategoryResource::collection($categories);
    }

    public function getByCategory(Tag $tag){

        $threads = $tag->threads()
        ->where('slide_image_pos', '!=', "")
            ->whereNotNull('slide_image_pos')
            ->where(function($q){
                if(!auth()->check() || !auth()->user()->is_admin){
                    $q->where('slide_ready',true);
                }
            })
            ->select($this->selectedFields)
            ->orderBy('updated_at', 'desc')
            ->paginate((int) request('per_page', 10))
            ;
        return  SlideResource::collection($threads);
    }


    public function buildQuery(){
        $query = Thread::query();
        $query->whereNotNull('slide_image_pos');
        $query->where('slide_image_pos','!=','');

        if(!auth()->check() || !auth()->user()->is_admin){
            $query->where('slide_ready',true);
        }
        $query->select($this->selectedFields);

        return $query;

    }
}
