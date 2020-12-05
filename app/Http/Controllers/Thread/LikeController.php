<?php

namespace App\Http\Controllers\Thread;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Thread;
use Illuminate\Http\Request;

class LikeController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        $thread->like();

        return \response(['success'=> true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Like  $like
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->dislike();

        return \response(['success'=> true]);
    }
}
