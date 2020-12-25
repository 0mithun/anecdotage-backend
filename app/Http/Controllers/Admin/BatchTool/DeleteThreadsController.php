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
        Thread::where('title', 'LIKE', "%{$request->delete_thread_title}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->delete();
            }
        });

        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }

    public function body(Request $request){
        $this->validate($request, [
            'delete_thread_body' =>  ['required']
        ],[
            'delete_thread_body.required'  => 'Delete thread body field is required.'
        ]);
        Thread::where('body', 'LIKE', "%{$request->delete_thread_body}%")->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->delete();
            }
        });

        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }

    public function tag(Request $request){
        $this->validate($request, [
            'delete_thread_tag' =>  ['required','exists:tags,name']
        ],[
            'delete_thread_tag.required' => 'Delete thread tag field is required.',
            'delete_thread_tag.exists' => 'Tag was not found',
        ]);
        $tag =  Tag::where('name',$request->delete_thread_tag)->first();

        $tag->threads()->chunk(100, function($threads){
            foreach($threads as $thread){
                $thread->delete();
            }
        });


        return \response(['success'=> true, ['message'=>'Thread Delete Successfully']], Response::HTTP_NO_CONTENT);
    }
}
