<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
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
        Gate::authorize('view-profile', $user);
        $user = $this->users->withCriteria([
            new EagerLoad(['usernotification'])
        ])->findWhereFirst('username', $user->username);


        return new UserResource($user);
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
        $user->usernotification()->update($request->only([
            'mention_notify_anecdotage',
            'mention_notify_email',
            'mention_notify_facebook',

            'new_thread_posted_notify_anecdotage',
            'new_thread_posted_notify_email',
            'new_thread_posted_notify_facebook',

            'receive_daily_random_thread_notify_anecdotage',
            'receive_daily_random_thread_notify_email',
            'receive_daily_random_thread_notify_facebook',
        ]));

        $user = $this->users->withCriteria([
            new EagerLoad(['usernotification'])
        ])->find($user->id);

        return response(new UserResource($user), Response::HTTP_ACCEPTED);

    }

 }
