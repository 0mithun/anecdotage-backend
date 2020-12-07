<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserbanResource extends JsonResource
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
            'ban_type'          => $this->ban_type,
            'ban_reason'        =>  $this->ban_reason,
            'ban_expire_on'     =>  $this->ban_expire_on,
            'user'              => $this->whenLoaded('user', function(){
                return new UserResource($this->user);
            })
        ];
    }
}
