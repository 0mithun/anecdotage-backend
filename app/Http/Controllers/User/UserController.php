<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
{

    protected $users;

    public function __construct(IUser $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        $users = $this->users->withCriteria([
        ])->all();

        return UserResource::collection($users);
    }

    public function search(Request $request)
    {
        $designers = $this->users->search($request);
        return UserResource::collection($designers);
    }

    public function findByUsername(User $user)
    {
        $user = $this->users->withCriteria([
            new EagerLoad(['threads','follows'])
        ])->findWhereFirst('username', $user->username);


        return (new UserResource($user))->additional([
            'data'  => [
                'followers' => $user->followers,
                'is_follow'         =>  $user->is_follow,
            ]
        ]);
    }
}
