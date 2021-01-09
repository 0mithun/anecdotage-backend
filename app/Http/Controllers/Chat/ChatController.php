<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\MessegeSentEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Notifications\NewMessageNotification;

class ChatController extends Controller
{
    protected $users;

    public function __construct(IUser $users)
    {
        $this->users = $users;
    }



    public function sendMessage( Request $request, User $user ) {
        $this->validate($request, [
            'message' =>  ['required'],
        ]);

        if($request->has('reply_id') && $request->reply_id != null){
            $message = Chat::create( [
                'from'           => \auth()->id(),
                'to'             => $user->id,
                'parent_id'             => $request->reply_id,
                'message'        => $request->message,
                'friend_message' =>  (bool) auth()->user()->isFriendWith($user),
            ] );
        }else{
            $message = Chat::create( [
                'from'           => \auth()->id(),
                'to'             => $user->id,
                'message'        => $request->message,
                'friend_message' =>  (bool) auth()->user()->isFriendWith($user),
            ] );
        }

        // $friend = User::where( 'id', $request->friend )->first();
        $user->notify( new NewMessageNotification( auth()->user(), $message ) );
        broadcast( new MessegeSentEvent( $message->load('parent') ) );
        return response()->json( $message->load('parent') );
    }

    public function lastSeen(User $user){
        $id = $user->id;

         $last_seen = Chat::where( function ( $q ) use ( $id ) {
            $q->where( 'from', auth()->user()->id );
            $q->where( 'to', $id );
        } )->orWhere( function ( $q ) use ( $id ) {
            $q->where( 'to', auth()->user()->id );
            $q->where( 'from', $id );
        } )->where( 'seen_at', '!=', null )
            ->orderBy( 'seen_at', 'DESC' )->first();

        return response()->json(['last_seeen' => $last_seen->seen_at]);
    }



    public function messageSeen( Request $request ) {
        $current_timestamp = now();
        $chat = Chat::where('id', $request->id)->update(['seen_at'=> $current_timestamp]);
        return response()->json(['last_seeen' => $current_timestamp]);
    }


    public function getFriendMessage(User $user ) {
        $id = $user->id;
        $messages = Chat::with(['parent'])->where( function ( $q ) use ( $id ) {
            $q->where( 'from', auth()->id() );
            $q->where( 'to', $id );
        } )->orWhere( function ( $q ) use ( $id ) {
            $q->where( 'to', auth()->id() );
            $q->where( 'from', $id );
        } )->get();

        return \response($messages);
    }

    public function getAllChatLists( Request $request ) {
        $friendLists = auth()->user()->getFriends()->pluck('id');
        $otherFromMessageUsers = Chat::
            where( 'to', auth()->id() )
            ->where( 'friend_message', 0 )
            ->distinct()
            ->pluck( 'from' );

        $otherToMessageUsers = Chat::
            where( 'from', auth()->id() )
            ->where( 'friend_message', 0 )
            ->distinct()
            ->pluck( 'to' );

        $all = collect([$friendLists, $otherFromMessageUsers, $otherToMessageUsers])->collapse()->toArray();

        $chatUserLists = $this->users->findWhereIn('id', $all);

        return UserResource::collection($chatUserLists);
    }

    public function notifications(){
        $notifications = auth()->user()->notifications()->where('type','App\Notifications\NewMessageNotification')->get();
        return response()->json(['notifications'=> $notifications ]);
    }

    public function markAsRead($id){
         auth()->user()->notifications()->where('type','App\Notifications\NewMessageNotification')->where('id', $id)->update(['read_at' => now()]);
    }

}
