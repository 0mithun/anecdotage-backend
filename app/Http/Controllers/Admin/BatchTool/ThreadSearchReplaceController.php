<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ThreadSearchReplaceController extends Controller
{
    public function title(Request $request){
        $request->validate([
            'replace_title_old' =>  'required',
            'replace_title_new' =>  'required',
        ],[
            'replace_title_old.reqired' => 'The old title field is required.',
            'replace_title_new.reqired' => 'The new title field is required.',
        ]);
        $old = $request->replace_title_old;
        $new =$request->replace_title_new;


    //   $threads =   DB::statement("UPDATE threads SET title= REPLACE(threads.title, '$old', '$new')");

        Thread::where('title', 'LIKE', "%{$request->replace_title_old}%")->chunk(100, function($threads) use($old, $new) {
            foreach($threads as $thread){
                $newTitle = preg_replace("/{$old}/i",$new, $thread->title);
                $thread->title = $newTitle;
                $thread->save();
            }
        });

        return response(['success'=> 'true', 'message'=> 'Thread update successfully'], Response::HTTP_ACCEPTED);
    }


    public function body(Request $request){
        $request->validate([
            'replace_body_old' =>  'required',
            'replace_body_new' =>  'required'
        ],[
            'replace_body_old.reqired' => 'The old body field is required.',
            'replace_body_new.reqired' => 'The new body field is required.'
        ]);
        $old = $request->replace_body_old;
        $new =$request->replace_body_new;

        Thread::where('body', 'LIKE', "%{$request->replace_body_old}%")->chunk(100, function($threads) use($old, $new) {
            foreach($threads as $thread){
                $newBody = preg_replace("/{$old}/i",$new, $thread->body);
                $thread->body = $newBody;
                $thread->save();
            }
        });

        return response(['success'=> 'true', 'message'=> 'Thread update successfully'], Response::HTTP_ACCEPTED);
    }
}
