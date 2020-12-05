<?php

namespace App\Http\Controllers\Tag;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Tag $tag)
    {
        $tag->follow();

        return response(['success'=> true], Response::HTTP_ACCEPTED);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->unfollow();
        return response(['success'=> true], Response::HTTP_NO_CONTENT);
    }
}
