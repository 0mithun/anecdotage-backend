<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'                => $this->id,
            'username'          => $this->username,
            $this->mergeWhen(auth()->check() && auth()->id() == $this->id, [
                'email'         => $this->email,
            ]),
            'name'              => $this->name,
            'date_of_birth'              => $this->date_of_birth,
            'photo_url'         => $this->photo_url,
            'create_dates'      => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at' => $this->created_at
            ],
            'formatted_address' => $this->formatted_address,
            'about'             => $this->about,
            'location'          =>  $this->location,

            $this->mergeWhen(auth()->check() && auth()->user()->is_admin && auth()->id() == $this->id, [
                'is_admin'          => $this->is_admin,
            ]),
            $this->mergeWhen(auth()->check() && auth()->user()->username  == $this->username, [
                'is_owner'          => true,
            ]),

            $this->mergeWhen(auth()->check() && auth()->user()->is_banned, [
                'is_banned'     => $this->is_banned,
            ]),
            // 'is_friend'         =>  $this->is_friend,
            'follow_type'       =>  $this->follow_type,
            'privacy'           => $this->whenLoaded('userprivacy', function(){
                return new UserPrivacyResource($this->userprivacy);
            }),
            'notification'           => $this->whenLoaded('usernotification', function(){
                return new UserNotificationResource($this->usernotification);
            }),
            'threads'           =>  ThreadResource::collection($this->whenLoaded('threads')),
            // 'followers'         =>  UserResource::collection($this->whenLoaded('follows', $this->followers))
        ];
    }
}
