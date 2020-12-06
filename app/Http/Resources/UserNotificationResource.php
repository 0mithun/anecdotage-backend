<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'mention_notify_anecdotage'                     =>         $this->mention_notify_anecdotage,
            'mention_notify_email'                          =>         $this->mention_notify_email,
            'mention_notify_facebook'                       =>         $this->mention_notify_facebook,
            'new_thread_posted_notify_anecdotage'           =>         $this->new_thread_posted_notify_anecdotage,
            'new_thread_posted_notify_email'                =>         $this->new_thread_posted_notify_email,
            'new_thread_posted_notify_facebook'             =>         $this->new_thread_posted_notify_facebook,
            'receive_daily_random_thread_notify_anecdotage' =>         $this->receive_daily_random_thread_notify_anecdotage,
            'receive_daily_random_thread_notify_email'      =>         $this->receive_daily_random_thread_notify_email,
            'receive_daily_random_thread_notify_facebook'   =>         $this->receive_daily_random_thread_notify_facebook,
        ];
    }
}
