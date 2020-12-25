<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserbanResource;
use App\Repositories\Contracts\IUserBan;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\Criteria\EagerLoad;

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

    public function title(Request $request){
        $request->validate([
            'ban_users_title' =>  'required',
            'ban_users_title_type' =>  'required',
            'ban_users_title_days' =>  ['required_if:ban_users_title_type,2'],
        ],[
            'ban_users_title.required'  => 'The title field is required.',
            'ban_users_title_type.required'  => 'The type field is required.',
            'ban_users_title_days.required_if'  => 'Days field is required.',
        ]);

        $userIds = Thread::
                    where('title', 'LIKE', "%{$request->ban_users_title}%")
                    ->distinct()
                    ->pluck('user_id')
                    ->all()
                    ;
        $this->banUsers($userIds, $request->ban_users_title_type, $request->ban_users_title_days);

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
    }

    public function body(Request $request){
        $request->validate([
            'ban_users_body' =>  'required',
            'ban_users_body_type' =>  'required',
            'ban_users_body_days' =>  ['required_if:ban_users_body_type,2'],
        ],[
            'ban_users_body.required'  => 'The body field is required.',
            'ban_users_body_type.required'  => 'The type field is required.',
            'ban_users_body_days.required_if'  => 'Days field is required.',
        ]);

        if((int) $request->ban_users_body_type == 2){
            $request->validate([
                'ban_users_body_days' =>  ['required'],
            ],[
                'ban_users_body_days.required'  => 'The day field is required.',
            ]);
        }

        $userIds = Thread::
                    where('body', 'LIKE', "%{$request->ban_users_body}%")
                    ->distinct()
                    ->pluck('user_id')
                    ->all()
                    ;
        $this->banUsers($userIds, $request->ban_users_body_type, $request->ban_users_body_days);

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
    }

    public function tag(Request $request){
        $request->validate([
            'ban_users_tag' =>  'required',
            'ban_users_tag_type' =>  'required',
            'ban_users_tag_days' =>  ['required_if:ban_users_tag_type,2'],
        ],[
            'ban_users_tag.required'  => 'The tag field is required.',
            'ban_users_tag_type.required'  => 'The type field is required.',
            'ban_users_tag_days.required_if'  => 'Days field is required.',
        ]);

        $tag = Tag::where('name',$request->ban_users_tag)->first();

        $userIds = $tag->threads()
                    ->distinct()
                    ->pluck('user_id')
                    ->all()
                    ;
        $this->banUsers($userIds, $request->ban_users_tag_type, $request->ban_users_tag_days);

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
    }


    public function banUsers($userIds, $type, $days){
        if($type==1){
            $expire =  NULL;
            $reason = 'You have violated our Terms of Service, your account has been permanently suspended.';
        }else{
            $expire = Carbon::now()->addDay($days)->toDateTimeString();
            $reason = "You have violated our Terms of Service, your account has been suspended for {$days} days";
        }
        $data = [
            'ban_type'      => $type,
            'ban_reason'    => $reason ,
            'ban_expire_on' =>   $expire,
        ];
         foreach($userIds as $id){
            $user = User::where('id', $id)->first();
            $user->userban()->create($data);

            // $user->notify(new UserBanNotification($reason));
        }
    }

    public function unbanAllUser(){
        DB::table('userbans')->delete();
        return response(['success'=> 'true', 'message'=> 'Users banned successfully'], Response::HTTP_NO_CONTENT);
    }


    public function banSingleUser(Request $request){
        $request->validate([
            'ban_user_username' =>  ['required','exists:users,username'],
            'ban_user_type' =>  ['required'],
            'ban_user_days' =>  ['required_if:ban_user_type,2'],
        ],[
            'ban_user_username.required'    => 'The username field is required.',
            'ban_user_username.exists'    => 'User not found.',
            'ban_user_type.required'        => 'The type field is required.',
            'ban_user_days.required_if'     => 'Days field is required.',
        ]);

        $user = User::where('username', $request->ban_user_username)->first();
        $this->banUsers([$user->id], $request->ban_user_type, $request->ban_user_days);

        return response(['success'=> 'true', 'message'=> 'User baned successfully'], Response::HTTP_CREATED);
    }


    public function unBanSingleUser(Request $request){
        $request->validate([
            'unban_user_username' =>  ['required','exists:users,username'],
        ],[
            'unban_user_username.required'    => 'The username field is required.',
            'unban_user_username.exists'    => 'User not found.',
        ]);

        $user = User::where('username', $request->unban_user_username)->first();
        $user->userban()->delete();
        return response(['success'=> 'true', 'message'=> 'User unbanned successfully'], Response::HTTP_NO_CONTENT);
    }
}
