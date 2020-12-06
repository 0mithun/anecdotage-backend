<?php

namespace App\Http\Controllers\Friend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FriendShipController extends Controller
{
    /**
     * Sent friend request to another user
     *
     * @param User $user
     * @return void
     */
    public function sentFriendRequestToUser(User $user){
        $authenticatedUser = auth()->user();
        if($this->checkIsBlock($user)){
            return response(['errors' => ['message'=> 'You are block by user or you block the user']],  Response::HTTP_UNAUTHORIZED);
        }
        else if($authenticatedUser->isFriendWith($user)){
            return response(['errors' => ['message'=> 'You are already friend with user']],  Response::HTTP_NOT_ACCEPTABLE);
        }else if($authenticatedUser->hasFriendRequestFrom($user)){
            return response(['errors' => ['message'=> 'You are already receive a friend request from the user']],  Response::HTTP_NOT_ACCEPTABLE);
        }else if($authenticatedUser->hasSentFriendRequestTo($user)){
            return response(['errors' => ['message'=> 'You are already sent a friend request to the user']],  Response::HTTP_NOT_ACCEPTABLE);
        }else if($authenticatedUser->username == $user->username){
            return response(['errors' => ['message'=> 'You  can not sent friend request to you']],  Response::HTTP_NOT_ACCEPTABLE);
        }

        $authenticatedUser->befriend($user);

        return \response(['success'=> true, 'message'=> 'Friend Request Sent Successfully'], Response::HTTP_CREATED);
    }


    /**
     * Accept Friend Request
     * @para User $user
     * @return mixed
     */

    public function acceptFriendRequest(User $user){
        $authenticatedUser = auth()->user();
        if($this->checkIsBlock($user)){
            return response(['errors' => ['message'=> 'You are block by user or you block the user']],  Response::HTTP_UNAUTHORIZED);
        }
        else if($authenticatedUser->isFriendWith($user)){
            return response(['errors' => ['message'=> 'You are already friend with user']],  Response::HTTP_NOT_ACCEPTABLE);
        }else if($authenticatedUser->hasSentFriendRequestTo($user)){
            return response(['errors' => ['message'=> 'You are already sent a friend request to the user']],  Response::HTTP_NOT_ACCEPTABLE);
        }
        else if(!$authenticatedUser->hasFriendRequestFrom($user)){
            return response(['errors' => ['message'=> 'User dose not sent friend request to you']],  Response::HTTP_NOT_FOUND);
        }
        $authenticatedUser->acceptFriendRequest($user);

        return \response(['success'=> true, 'message'=> 'Friend Request Accept Successfully'], Response::HTTP_CREATED);
    }


    /**
     * Denied friend request
     * @param User $user
     * @return mixed
     */

    public function deniedFriendRequest(User $user){
        $authenticatedUser = auth()->user();
        if($this->checkIsBlock($user)){
            return response(['errors' => ['message'=> 'You are block by user or you block the user']],  Response::HTTP_UNAUTHORIZED);
        }
        else if($authenticatedUser->isFriendWith($user)){
            return response(['errors' => ['message'=> 'You are already friend with user']],  Response::HTTP_NOT_ACCEPTABLE);
        }else if($authenticatedUser->hasSentFriendRequestTo($user)){
            return response(['errors' => ['message'=> 'You are already sent a friend request to the user']],  Response::HTTP_NOT_ACCEPTABLE);
        }
        else if(!$authenticatedUser->hasFriendRequestFrom($user)){
            return response(['errors' => ['message'=> 'User dose not sent friend request to you']],  Response::HTTP_NOT_FOUND);
        }
        $authenticatedUser->denyFriendRequest($user);

        return \response(['success'=> true, 'message'=> 'Friend Request Denied Successfully'], Response::HTTP_ACCEPTED);
    }


    /**
     * Unfriend user
     * @param User $user
     * @return mixed
     */

    public function unfriendUser(User $user){
        $authenticatedUser = auth()->user();
        if($this->checkIsBlock($user)){
            return response(['errors' => ['message'=> 'You are block by user or you block the user']],  Response::HTTP_UNAUTHORIZED);
        }
        else if(!$authenticatedUser->isFriendWith($user)){
            return response(['errors' => ['message'=> 'You are not friend with user']],  Response::HTTP_NOT_ACCEPTABLE);
        }
        $authenticatedUser->unfriend($user);

        return \response(['success'=> true, 'message'=> 'Friend Unfriend Successfully'], Response::HTTP_NO_CONTENT);
    }


    /**
     * Block a friend by user
     * @param User $user
     * @return mixed
     */

    public function blockFriend(User $user){
        $authenticatedUser = auth()->user();

        if($authenticatedUser->hasBlocked($user)){
            return response(['errors' => ['message'=> 'You already block user']],  Response::HTTP_UNAUTHORIZED);
        }else if($authenticatedUser->isBlockedBy($user)){
            return response(['errors' => ['message'=> 'User already blocked you']],  Response::HTTP_UNAUTHORIZED);
        }
        $authenticatedUser->blockFriend($user);

        return \response(['success'=> true, 'message'=> 'Friend Block Successfully'], Response::HTTP_ACCEPTED);
    }


    /**
     * Unblock a friend
     * @param User $user
     * @return mixed
     */

    public function unblockUesr(User $user){
        $authenticatedUser = auth()->user();

        $authenticatedUser = auth()->user();
        if(!$authenticatedUser->hasBlocked($user)){
            return response(['errors' => ['message'=> 'User not blocked by you']],  Response::HTTP_UNAUTHORIZED);
        }else if($authenticatedUser->isBlockedBy($user)){
            return response(['errors' => ['message'=> 'User already blocked you']],  Response::HTTP_UNAUTHORIZED);
        }
        $authenticatedUser->unblockFriend($user);

        return \response(['success'=> true, 'message'=> 'Friend Unblock Successfully'], Response::HTTP_ACCEPTED);
    }


    /**
     * Check is authenticated user is friend with the user
     * @param User $user
     * @return mixed
     */

    public function checkIsUserFriendWith(User $user){
        $authenticatedUser = auth()->user();
        $isFriend = (bool) $authenticatedUser->isFriendWith($user);

        return \response(['is_friend' => $isFriend]);
    }


    /**
     * Check authenticated user has get friend request from user
     * @param  User $user
     * @return mixed
     */

    public function checkHasSentFriendRequestFrom(User $user){
        $authenticatedUser = auth()->user();
        $get_friend_request_from = (bool) $authenticatedUser->hasFriendRequestFrom($user);

         return \response(['sent_friend_request_from' => $get_friend_request_from]);
    }


    /**
     * Check authenticatd user sent frined request to user
     * @param User $user
     * @return mixed
     */

    public function checkHasSentFriendRequestTo(User $user){
        $authenticatedUser = auth()->user();
        $set_friend_request_to = (bool) $authenticatedUser->hasSentFriendRequestTo($user);

         return \response(['sent_friend_request_to' => $set_friend_request_to]);
    }


    /**
     * Check is user block the user or user block by the user
     * @param User $user
     * @return mixed
     */

    public function checkIsBlock(User $user){
        $authenticatedUser = auth()->user();
        $isBlock = (bool) $authenticatedUser->hasBlocked($user) || $authenticatedUser->isBlockedBy($user);
        return \response(['is_block' => $isBlock]);
    }



    /**
     * Get all friend list for user
     * @param User $user
     * @return mixed
     */

    public function getAllFriendLists(User $user){
        // Gate::authorize('view-own-friendship', $user);
        Gate::authorize('view-friends', $user->load('userprivacy'));

        $friends =  $user->getFriends();

        return UserResource::collection($friends);
    }


    /**
     * Get all block friend lists
     * @param User @user
     * @return mixed
     */

    public function getAllBlokcFriends(User $user){
        Gate::authorize('view-own-friendship', $user);

        $blocckUserId = [];
       foreach( $user->getBlockedFriendships() as $friend){
           if($friend->sender_id === $user->id){
               $blocckUserId[] = $friend->recipient_id;
           }
       }
       $blockFriends = User::whereIn('id', $blocckUserId)->get();

        return UserResource::collection($blockFriends);
    }


    /**
     * Get all pending friend requests
     *
     * @param User $user
     * @return void
     */
    public function getAllPendingFriendRequests(User $user){
        Gate::authorize('view-own-friendship', $user);

        $pendingRequestsId = [];
        foreach( $user->getPendingFriendships() as $friend){
                $pendingRequestsId[] = $friend->sender_id;
        }
        $pendingFriends = User::whereIn('id', $pendingRequestsId)->get();

        return UserResource::collection($pendingFriends);
    }

}
