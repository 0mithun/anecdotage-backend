<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserbanResource;
use App\Models\User;
use App\Repositories\Contracts\IUserBan;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BanController extends Controller
{

    protected $userBans;

    public function __construct(IUserBan $userBans)
    {
        $this->userBans = $userBans;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bannedUsers = $this->userBans->withCriteria([
            new EagerLoad(['user'])
        ])->all();

        return response(UserbanResource::collection($bannedUsers));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->validate($request, [
            'ban_expire_on' => ['date']
        ]);

        if($user->is_banned){
            return response(['sucess'=>false,'message'=>'User already banned'], Response::HTTP_NOT_ACCEPTABLE);
        }
        $user->userban()->create($request->only(['ban_type','ban_reason','ban_expire_on']));

        return response(['sucess'=>true,'message'=>'User ban successfully'], Response::HTTP_CREATED);
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
        $bannedUser = $this->userBans->findWhereFirst('user_id', $user->id);
        if(!$bannedUser){
            return \response(['errors'=>['message'   => 'The resource was not found in the database']], Response::HTTP_NOT_FOUND);
        }
        $this->userBans->update($bannedUser->id, $request->only(['ban_type','ban_reason','ban_expire_on']));

        return response(['sucess'=>true,'message'=>'User ban upadate successfully'], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $bannedUser = $this->userBans->findWhereFirst('user_id', $user->id);
        if(!$bannedUser){
            return \response(['errors'=>['message'   => 'The resource was not found in the database']], Response::HTTP_NOT_FOUND);
        }
        $this->userBans->delete($bannedUser->id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
