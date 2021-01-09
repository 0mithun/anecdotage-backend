<?php

namespace App\Listeners;

use App\Models\User;


use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\Notifications\YouWereMentionedEmail;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        //dd($event->reply->mentionedUsers());

        ///@(?<=@)[a-zA-Z]+\s[a-zA-Z]+/

        preg_match_all('/@([\w\-]+\s[a-zA-Z]+)/', $event->reply->body, $matches);
        //dd($matches);


        foreach ($matches[1] as $name){
            if($user = User::where('name', $name)->first()){
                $usersettings = $user->usersetting;
                if($usersettings->mention_notify_anecdotage ==1){
                    $user->notify(new YouWereMentioned($event->reply));
                }
                if($usersettings->mention_notify_email ==1){
                    $user->notify(new YouWereMentionedEmail($event->reply));
                }
            }
        }
//        User::whereIn('name', $event->reply->mentionedUsers())
//            ->get()
//            ->each(function ($user) use ($event) {
//                $user->notify(new YouWereMentioned($event->reply));
//            });
    }
}
