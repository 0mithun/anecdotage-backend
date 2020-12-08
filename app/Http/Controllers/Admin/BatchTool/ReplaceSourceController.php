<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReplaceSourceController extends Controller
{
    public function replace(Request $request){
        $request->validate([
            'old_source' =>  'required',
            'new_source' =>  'required'
        ]);

        $threads = Thread::where('source', 'LIKE', "%{$request->old_source}%")->chunk(100, function($threads) use($request){
           foreach($threads as $thread){
                $pattern = "/{$request->old_source}/i";
                $replacement = $request->new_source;
                $newSource = preg_replace($pattern, $replacement, $thread->source);
                $thread->source = $newSource;
                $thread->save();
           }
        });

        return response(['success'=> true, 'message'=>'Thread source replace successfully'], Response::HTTP_ACCEPTED);
    }
}
