<?php

namespace App\Http\Controllers\User;

use DB;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use App\Repositories\Contracts\IUser;

use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ThreadSort;

class ProfileController extends Controller
{
    protected $users;
    protected $threads;

    public function __construct(IUser $users, IThread $threads)
    {
        $this->middleware(['auth:api'])->only(['likes','subscriptions']);

        $this->users = $users;
        $this->threads = $threads;
    }


    /**
     * Get profile user info
     * @param User $user
     * @return mixed
     */
    public function user(User $user){
        Gate::authorize('view-profile', $user);
        $user = $this->users->withCriteria([
            new EagerLoad(['userprivacy'])
        ])->findWhereFirst('username', $user->username);


        return (new UserResource($user))->additional([
            'data'  => [
                // 'followers'         => $user->followers,
                'is_follow'         =>  $user->is_follow,
                'is_friend'         =>  $user->is_friend,
                'is_blocked'         =>  $user->is_blocked,
            ]
        ]);
    }


    /**
     * Get profile user subscriptions threads
     * @param User $user
     * @return mixed
     */
    public function subscriptions(User $user){
        Gate::authorize('own-profile', $user);
        $subscriptionsId = DB::table( 'thread_subscriptions' )
            ->where( 'user_id', $user->id )
            ->get()
            ->pluck( 'thread_id' )
            ->all();

        $threads = $this->threads->withCriteria([
            new ThreadSort(),
            new EagerLoad(['creator','emojis'])
        ])->findWhereInPaginate('id', $subscriptionsId);

        return ThreadResource::collection($threads);
    }



    /**
     * Get profile user favorites threads
     * @param User $user
     * @return mixed
     */
    public function favorites(User $user){
        Gate::authorize('view-favorites', $user->load('userprivacy'));

        $favoritesId = DB::table( 'favorites' )
        ->where( 'user_id', $user->id )
        ->where('favorited_type', 'App\Models\Thread')
        ->get()
        ->pluck( 'favorited_id' )
        ->all();

        $threads = $this->threads->withCriteria([
            new ThreadSort(),
            new EagerLoad(['creator','emojis'])
        ])->findWhereInPaginate('id', $favoritesId);

        return ThreadResource::collection($threads);
    }



    /**
     * Get profile user likes threads
     *
     * @param User $user
     * @return mixed
     */
    public function likes(User $user){
        Gate::authorize('own-profile', $user);
        $likesId = DB::table( 'likes' )
        ->where( 'user_id', $user->id )
        ->where('likeable_type', 'App\Models\Thread')
        ->get()
        ->pluck( 'likeable_id' )
        ->all();

        $threads = $this->threads->withCriteria([
            new ThreadSort(),
            new EagerLoad(['emojis','creator'])
        ])->findWhereInPaginate('id', $likesId);

        return ThreadResource::collection($threads);
    }



    /**
     * Get profile user threads
     *
     * @param User $user
     * @return mixed
     */
    public function threads(User $user){
        Gate::authorize('view-threads', $user->load('userprivacy'));
        $threadsId = $user->threads()->pluck('id')->toArray();

        $threads = $this->threads->withCriteria([
            new ThreadSort(),
            new EagerLoad(['emojis','creator'])
        ])->findWhereInPaginate('id', $threadsId);

        return ThreadResource::collection($threads);
    }

}
