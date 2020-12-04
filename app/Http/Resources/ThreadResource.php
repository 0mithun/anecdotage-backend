<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResource extends JsonResource
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
            'id'                        =>  $this->id,
            'title'                     =>  $this->title,
            'slug'                      =>  $this->slug,
            'body'                      =>  $this->body,
            'source'                    =>  $this->source,
            'main_subject'              => $this->main_subject,
            'image_path'                =>  $this->image_path,
            'image_path_pixel_color'    => $this->image_path_pixel_color,
            'image_description'         => $this->image_description,
            'cno'                       =>  $this->cno,
            'age_restriction'           =>  $this->age_restriction,
            'anonymous'                 =>  $this->anonymous,
            'location'                  =>  $this->location,
            'is_published'              =>  $this->is_published,
            'creator'                   =>  new UserResource($this->creator),
            'tags'                      =>  TagResource::collection($this->tags),
            'emojis'                    =>  EmojiResource::collection($this->emojis)
        ];
    }
}
