<?php

namespace App\Http\Controllers\Thread;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Thread;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        $thread->favorite();
        return \response(['success'=>true], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Favorite  $favorite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->unfavorite();

        return \response(['success'=>true], Response::HTTP_NO_CONTENT);
    }
}
