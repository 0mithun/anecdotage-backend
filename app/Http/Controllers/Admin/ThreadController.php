<?php

namespace App\Http\Controllers\Admin;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Jobs\WikiImageProcess;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class ThreadController extends Controller
{
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function update(Request $request, Thread $thread)
    {
        $this->validate($request, [
            'title'     =>  ['required'],
        ]);

        $title = $request->title;

        /*
        * Old instructions
        */

        /*

        $title = str_replace('*','', $title);
        $data = [
            'title' =>  $title
        ];

        if ($request->has('title') && auth()->user()->is_admin) {
            $slug = str_slug(strip_tags( $title));
            if($slug != $thread->slug){
                $data['slug'] = $title;
            }
        }

        // $thread->update(['title'=> $title, 'slug'=>$title]);
        // $thread->update(['title'=> $title]);
        $thread->update($data);
        $thread = $thread->fresh();

        $split_title = preg_split("@(\*)@", $request->title);
        // $split_title = preg_split("@('|:|-|\*)@", $request->title);
        if (count($split_title) > 0 && $split_title[0] != '') {
            $keyword = $split_title[0];
            dispatch(new WikiImageProcess($keyword, $thread));
        }

        */

        /**
         * New Instructions
         */

        $title = str_replace('[','', $title);
        $title = str_replace(']','', $title);
        $data = [
            'title' =>  $title
        ];

        // $title = preg_replace("#('.\s)#",' ',$title);
        $title = preg_replace("#('",'',$title);

        $slug = str_slug(strip_tags( $title));
        if($slug != $thread->slug){
            $data['slug'] = $title;
        }

        $thread->update($data);
        $thread = $thread->fresh();


        if(preg_match("/\[(.*)\]/", $request->title, $matches)){
            dispatch(new WikiImageProcess($matches[1], $thread));
        }


        return response(['success'=>true,'thread'=> $thread]);
    }

    public function sortByTitleLength(){
          $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->orderByRaw('length(title)')->paginate();
        return  ThreadResource::collection($threads);
    }

}
