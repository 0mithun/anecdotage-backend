<?php
    namespace App\Models\Traits;

use Hootlex\Friendships\Models\Friendship;
use Hootlex\Friendships\Models\FriendFriendshipGroups;
use Hootlex\Friendships\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

use Hootlex\Friendships\Traits\Friendable;

trait FriendShipTrait{
    use Friendable;

     /**
     * @param Model $recipient
     *
     * @return \Hootlex\Friendships\Models\Friendship|false
     */
    public function befriend(Model $recipient)
    {

        if (!$this->canBefriend($recipient)) {
            return false;
        }

        $friendship = (new Friendship)->fillRecipient($recipient)->fill([
            'status' => Status::PENDING,
        ]);

        $this->friends()->save($friendship);

        Event::dispatch('friendships.sent', [$this, $recipient]);

        return $friendship;

    }

    /**
     * @param Model $recipient
     *
     * @return bool
     */
    public function unfriend(Model $recipient)
    {
        $deleted = $this->findFriendship($recipient)->delete();

        Event::dispatch('friendships.cancelled', [$this, $recipient]);

        return $deleted;
    }


    /**
     * @param Model $recipient
     *
     * @return bool|int
     */
    public function acceptFriendRequest(Model $recipient)
    {
        $updated = $this->findFriendship($recipient)->whereRecipient($this)->update([
            'status' => Status::ACCEPTED,
        ]);

        Event::dispatch('friendships.accepted', [$this, $recipient]);

        return $updated;
    }

    /**
     * @param Model $recipient
     *
     * @return bool|int
     */
    public function denyFriendRequest(Model $recipient)
    {
        $updated = $this->findFriendship($recipient)->whereRecipient($this)->update([
            'status' => Status::DENIED,
        ]);

        Event::dispatch('friendships.denied', [$this, $recipient]);

        return $updated;
    }





    /**
     * @param Model $recipient
     *
     * @return \Hootlex\Friendships\Models\Friendship
     */
    public function blockFriend(Model $recipient)
    {
        // if there is a friendship between the two users and the sender is not blocked
        // by the recipient user then delete the friendship
        if (!$this->isBlockedBy($recipient)) {
            $this->findFriendship($recipient)->delete();
        }

        $friendship = (new Friendship)->fillRecipient($recipient)->fill([
            'status' => Status::BLOCKED,
        ]);

        $this->friends()->save($friendship);

        Event::dispatch('friendships.blocked', [$this, $recipient]);

        return $friendship;
    }

    /**
     * @param Model $recipient
     *
     * @return mixed
     */
    public function unblockFriend(Model $recipient)
    {
        $deleted = $this->findFriendship($recipient)->whereSender($this)->delete();

        Event::dispatch('friendships.unblocked', [$this, $recipient]);

        return $deleted;
    }

}
