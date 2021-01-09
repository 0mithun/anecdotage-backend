<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCreator
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        $creator = $event->reply->thread->creator;

        $creator->notify(new ThreadWasUpdated($event->reply->thread, $event->reply));

        
    }
}
