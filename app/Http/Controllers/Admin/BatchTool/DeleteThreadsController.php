<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeleteThreadsController extends Controller
{
    public function title(Request $request){
        $this->validate($request, [
            'delete_thread_title' =>  ['required']
        ],[
            'delete_thread_title.required'  => 'Delete thread title field is required.'
        ]);
        $threads = Thread::where('title', 'LIKE', "%{$request->delete_thread_title}%")->get();
        $threads->each(function($thread){
            $thread->delete();
        });


        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }

    public function body(Request $request){
        $this->validate($request, [
            'delete_thread_body' =>  ['required']
        ],[
            'delete_thread_body.required'  => 'Delete thread body field is required.'
        ]);
        $threads = Thread::where('body', 'LIKE', "%{$request->delete_thread_body}%")->get();
        $threads->each(function($thread){
            $thread->delete();
        });
        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }

    public function tag(Request $request){
        $this->validate($request, [
            'delete_thread_tag' =>  ['required']
        ],[
            'delete_thread_tag.required' => 'Delete thread tag field is required.'
        ]);
        $tag = Tag::findOrFail($request->delete_thread_tag);

        $tag->threads->each(function($thread){
            $thread->delete();
        });

        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }
}
