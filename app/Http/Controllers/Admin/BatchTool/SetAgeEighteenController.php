<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class SetAgeEighteenController extends Controller
{
    public function title(Request $request){
        $this->validate($request, [
            'title_18' =>  'required'
        ],[
            'title_18.reqired' => 'The title field is required.'
        ]);
        Thread::where('title', 'LIKE', "%{$request->title_18}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->update(['age_restriction'=>18]);
            }
        });

        return \response(['success'=> true, ['message'=>'Thread Age Restriction 18 Set Successfully']], Response::HTTP_ACCEPTED);
    }


    public function body(Request $request){
        $this->validate($request, [
            'body_18' =>  'required'
        ],[
            'body_18.reqired' => 'The body field is required.'
        ]);
        Thread::where('body', 'LIKE', "%{$request->body_18}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->update(['age_restriction'=>18]);
            }
        });

        return \response(['success'=> true, ['message'=>'Thread Age Restriction 18 Set Successfully']], Response::HTTP_ACCEPTED);
    }

    public function tag(Request $request){
        $request->validate([
            'tag_18' =>  'required'
        ],[
            'tag_18.reqired' => 'The tag field is required.'
        ]);

        $tag = Tag::findOrFail($request->tag_18);

        $threadsId = $tag->threads()->pluck('id')->toArray();
        Thread::whereIn('id', $threadsId)->update(['age_restriction'=>18]);

        return \response(['success'=> true, ['message'=>'Thread Age Restriction 18 Set Successfully']], Response::HTTP_ACCEPTED);
    }
}
