<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'id'                =>  $this->id,
            'name'              =>  $this->name,
            'slug'              =>  $this->slug,
            'photo'             =>  $this->photo,
            'description'       =>  $this->description,
            'photo_url'         =>  $this->photo_url,
            'follow_type'        =>  $this->followType,
            // 'threads'           =>  ThreadResource::collection($this->whenLoaded('threads'))

        ];
    }
}
