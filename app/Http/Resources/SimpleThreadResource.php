<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleThreadResource extends JsonResource
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
            'excerpt'                   =>  $this->excerpt,
            'thread_image_path'         =>  $this->thread_image_path,

            'image_path_pixel_color'    =>  $this->image_path_pixel_color,

            'anonymous'                 =>  $this->anonymous,
            'location'                  =>  $this->location,
            'formatted_address'         =>  $this->formatted_address,
            'favorites_count'           =>  $this->favorite_count,
            'visits'                    =>  $this->visits,
            'is_favorited'              =>  $this->is_favorited,
            'user_emoji_vote'           =>   new EmojiResource($this->user_emoji_vote),
            'is_liked'                  =>  $this->is_liked,
            'is_disliked'               =>  $this->is_disliked,
            'likes_count'               =>  $this->like_count,
            'dislikes_count'            =>  $this->dislike_count,
            'points'                    =>  $this->points,
            'replies_count'             =>  $this->replies->count(),
            'channel'                   =>  $this->whenLoaded('channel', function(){
                return new ChannelResource($this->channel);
            }),

            'emojis'                    =>  EmojiResource::collection($this->whenLoaded('emojis')),
            'is_owner'                  => $this->is_owner,



        ];
    }
}
