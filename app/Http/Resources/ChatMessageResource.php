<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
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
            '_id'   => $this->id,
            'content'   => $this->content,
            'sender_id' => $this->sender_id,
            'username'  => $this->username,
            'date'  => $this->date,
            'timestamp' => $this->timestamp,
            'system'    => $this->system,
            'saved' => $this->saved,
            'distributed'   => $this->distributed,
            'seen'  => $this->seen,
            'disable_actions'   => $this->disable_actions,
            'disable_reactions' => $this->disable_reactions,
            'messages'    => ChatMessageResource::collection($this->childs),
        ];
    }
}
