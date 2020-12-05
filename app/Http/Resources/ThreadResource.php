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
            'user_id'                   =>  $this->user_id,
            'channel_id'                =>  $this->channel_id,
            'title'                     =>  $this->title,
            'slug'                      =>  $this->slug,
            'body'                      =>  $this->body,
            'source'                    =>  $this->source,
            'main_subject'              =>  $this->main_subject,
            'image_path'                =>  $this->image_path,
            'image_path_pixel_color'    =>  $this->image_path_pixel_color,
            'image_description'         =>  $this->image_description,
            'cno'                       =>  $this->cno,
            'age_restriction'           =>  $this->age_restriction,
            'anonymous'                 =>  $this->anonymous,
            'location'                  =>  $this->location,
            'favorites_count'           =>  $this->favorites_count,
            'is_favorited'              =>  $this->is_favorited,
            'is_published'              =>  $this->is_published,
            'is_voted'                  =>  $this->is_voted,
            'is_liked'                  =>  $this->is_liked,
            'is_disliked'               =>  $this->is_disliked,
            'likes_count'               =>  $this->likes_count,
            'dislikes_count'            =>  $this->dislikes_count,
            'creator'                   =>  new UserResource($this->creator),
            'tags'                      =>  TagResource::collection($this->whenLoaded('tags')),
            'emojis'                    =>  EmojiResource::collection($this->emojis)
        ];
    }
}
