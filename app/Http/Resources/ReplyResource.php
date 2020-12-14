<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
            'id'        => $this->id,
            'body'      => $this->body,
            'thread_id' => $this->thread_id,
            'parent_id' => $this->parent_id,
            'replies_count' => $this->replies_count,
            // 'childs'    =>  ReplyResource::collection($this->childs)
            // 'owner'     => $this->owner,
            'owner'     =>  $this->whenLoaded( 'owner', function(){
                return new UserResource($this->owner);
            }),
            'created_at'     => $this->created_at
        ];
    }
}
