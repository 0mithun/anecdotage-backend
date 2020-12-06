<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPrivacyResource extends JsonResource
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
            'see_my_profiles'           => $this->see_my_profiles,
            'see_my_threads'            => $this->see_my_threads,
            'see_my_favorites'          => $this->see_my_favorites,
            'see_my_friends'            => $this->see_my_friends,
            'send_me_message'           => $this->send_me_message,
            'thread_create_share_facebook' => $this->thread_create_share_facebook,
            'thread_create_share_twitter'  => $this->thread_create_share_twitter,
            'anyone_share_my_thread_facebook'  => $this->anyone_share_my_thread_facebook,
            'anyone_share_my_thread_twitter'   => $this->anyone_share_my_thread_twitter,
            'restricted_13'             => $this->restricted_13,
            'restricted_18'             => $this->restricted_18
        ];
    }
}
