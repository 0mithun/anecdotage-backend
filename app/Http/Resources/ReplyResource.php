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
            'parent'    => new ReplyResource($this->parent),
            'replies_count' => $this->replies_count,
            // 'childs'    =>  ReplyResource::collection($this->childs)
            'owner'     =>  new UserResource($this->owner),
        ];
    }
}
