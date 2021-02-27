<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\WikiImageProcess;
use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function update(Request $request, Thread $thread)
    {
        $this->validate($request, [
            'title'     =>  ['required'],
        ]);

        $title = $request->title;


        // $split_title = explode("*", $title);
        // if (count($split_title) > 0 && $split_title[0] != '') {
        //     $keyword = $split_title[0];
        //     dispatch(new WikiImageProcess($keyword, $thread));
        // }

        $title = str_replace('*','', $title);


        $thread->update(['title'=> $title, 'slug'=>$title]);
        $thread = $thread->fresh();

        $split_title = preg_split("@('|:|-|\*)@", $request->title);
        if (count($split_title) > 0 && $split_title[0] != '') {
            $keyword = $split_title[0];
            dispatch(new WikiImageProcess($keyword, $thread));
        }

        return response(['success'=>true,'thread'=> $thread]);
    }

}
