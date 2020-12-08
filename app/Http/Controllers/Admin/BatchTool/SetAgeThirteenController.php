<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class SetAgeThirteenController extends Controller
{

    public function title(Request $request){
        $this->validate($request,[
            'title_13' =>  ['required']
        ],[
            'title_13.required'  => 'The title field is required.'
        ]);
        Thread::where('title', 'LIKE', "%{$request->title_13}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->update(['age_restriction'=>13]);
            }
        });


        return \response(['success'=> true, ['message'=>'Thread Age Restriction 13 Set Successfully']], Response::HTTP_NO_CONTENT);
    }


    public function body(Request $request){
        $this->validate($request, [
            'body_13' =>  ['required']
        ],[
            'body_13.required'  => 'The body field is required.'
        ]);
         Thread::where('body', 'LIKE', "%{$request->body_13}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->update(['age_restriction'=>13]);
            }
        });

        return \response(['success'=> true, ['message'=>'Thread Age Restriction 13 Set Successfully']], Response::HTTP_NO_CONTENT);
    }


    public function tag(Request $request){
        $this->validate($request, [
            'tag_13' =>  ['required']
        ],[
            'tag_13.required' => 'The tag field is required.'
        ]);
        $tag = Tag::findOrFail($request->tag_13);
        $threadsId = $tag->threads()->pluck('id')->toArray();
        Thread::whereIn('id', $threadsId)->update(['age_restriction'=>13]);

        return \response(['success'=> true, ['message'=>'Thread Age Restriction 13 Set Successfully']], Response::HTTP_NO_CONTENT);
    }
}
