<?php

namespace App\Http\Controllers\User;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $user->follow();

        return response(['success'=> true], Response::HTTP_ACCEPTED);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->unfollow();
        return response(['success'=> true], Response::HTTP_NO_CONTENT);
    }

    public function followers(User $user ) {
        $followersId = DB::table( 'follows' )->where( 'followable_id', $user->id )->where( 'followable_type', 'App\Models\User' )->get()->pluck( 'user_id' );

        $followings = User::whereIn( 'id', $followersId )->get();
        $users = UserResource::collection($followings);
        return response()->json( ['followers' => $users] );
    }

    /**
     * get all following
     * @param string $user
     * @return App\User
     */

    public function followings(User $user ) {
        $userFollowingId = DB::table( 'follows' )->where( 'user_id', $user->id )->where( 'followable_type', 'App\Models\User' )->get()->pluck( 'followable_id' );
        $userFollowings = User::whereIn( 'id', $userFollowingId )->get();
        $users = UserResource::collection($userFollowings);

        $tagFollowingId = DB::table( 'follows' )->where( 'user_id', $user->id )->where( 'followable_type', 'App\Models\User' )->get()->pluck( 'followable_id' );
        $tagFollowings = Tag::whereIn( 'id', $tagFollowingId )->get();
        $tags = TagResource::collection($tagFollowings);

        $data = collect( $users );
        $followings = $data->merge( $tags );

        return response()->json( ['followings' => $followings->all()] );

    }
}
