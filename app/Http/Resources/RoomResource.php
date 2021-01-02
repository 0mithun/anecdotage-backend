<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'roomId'    => $this->id,
            'roomName'  =>  $this->name,
            'avatar'    =>  $this->avatar,
            'lastMessage'   => new ChatMessageResource($this->last_message),
            'users'     => $this->whenLoaded('users', function(){
                return RoomUserResource::collection($this->users);
            })
        ];
    }
}
