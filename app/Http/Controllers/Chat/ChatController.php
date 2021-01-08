<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;

class ChatController extends Controller
{
    protected $users;

    public function __construct(IUser $users)
    {
        $this->users = $users;
    }



    public function sendMessage( Request $request ) {

        $message = Chat::create( [
            'from'           => \auth()->id(),
            'to'             => $request->recipient,
            'message'        => $request->message,
            'friend_message' => $request->is_friend,
            'reply_id'       => $request->replyId,
            'reply_message'  => $request->replyMessage,
        ] );

        $friend = User::where( 'id', $request->friend )->first();

        // $friend->notify( new NewMessageNotification( $authUser, $message ) );

        // broadcast( new MessegeSentEvent( $message ) );

        return response()->json( $message );


    }



    public function seenMessage( Request $request ) {
        $chat = Chat::find( $request->message );

        $current_timestamp = now();

        $chat->seen_at = $current_timestamp;
        $chat->save();

        $chat = $chat->fresh();

        return $chat;

    }


    public function getFriendMessage(User $user ) {
        $id = $user->id;
        $messages = Chat::where( function ( $q ) use ( $id ) {
            $q->where( 'from', auth()->id() );
            $q->where( 'to', $id );
        } )->orWhere( function ( $q ) use ( $id ) {
            $q->where( 'to', auth()->id() );
            $q->where( 'from', $id );
        } )->get();

        // $last_seen = Chat::where( function ( $q ) use ( $id ) {
        //     $q->where( 'from', auth()->user()->id );
        //     $q->where( 'to', $id );
        // } )->orWhere( function ( $q ) use ( $id ) {
        //     $q->where( 'to', auth()->user()->id );
        //     $q->where( 'from', $id );
        // } )->where( 'seen_at', '!=', null )
        //     ->orderBy( 'seen_at', 'DESC' )->first();

        return \response($messages);

        // return response()->json( [
        //     'messages'  => $messages,
        //     'last_seen' => $last_seen,
        // ] );
    }

    public function getOtherMessageUsers() {
        $authUser = auth()->user();

        $otherFromMessageUsers = Chat::
            where( 'to', $authUser->id )
            ->where( 'friend_message', 0 )
            ->distinct()
            ->pluck( 'from' );

        $otherToMessageUsers = Chat::
            where( 'from', $authUser->id )
            ->where( 'friend_message', 0 )
            ->distinct()
            ->pluck( 'to' );

        $otherMessageUsers = $otherFromMessageUsers->merge( $otherToMessageUsers );

        $otherUsers = User::whereIn( 'id', $otherMessageUsers )->get();

        return \response()->json(['users'=> $otherUsers]);
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
}
