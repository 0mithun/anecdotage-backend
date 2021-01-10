<?php

namespace App\Models;

use App\Models\User;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class ThreadSubscription extends Model
{
    use Notifiable;

    protected $table = 'thread_subscriptions';

    protected $fillable = [
        'user_id','thread_id'
    ];

      /**
     * Get the user associated with the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the thread associated with the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    // /**
    //  * Notify the related user that the thread was updated.
    //  *
    //  * @param \App\Reply $reply
    //  */
    // public function notify($reply)
    // {
    //     $this->user->notify(new ThreadWasUpdated($this->thread, $reply));
    // }

}
