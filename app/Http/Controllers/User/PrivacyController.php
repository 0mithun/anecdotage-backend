<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PrivacyController extends Controller
{
    protected $users;

    public function __construct(IUser $users)
    {
        $this->users = $users;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('edit-profile', $user);
        $user->userprivacy()->update($request->only([
            'see_my_profiles',
            'see_my_threads',
            'see_my_favorites',
            'see_my_friends',
            'send_me_message',
            'thread_create_share_facebook',
            'thread_create_share_twitter',
            'anyone_share_my_thread_facebook',
            'anyone_share_my_thread_twitter',
            'restricted_13',
            'restricted_18'
        ]));

       $user = $this->users->withCriteria([
           new EagerLoad('userprivacy')
       ])->find($user->id);

       return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

}
