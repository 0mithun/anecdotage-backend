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
            'excerpt'                   =>  $this->excerpt,
            'source'                    =>  $this->source,
            'main_subject'              =>  $this->main_subject,
            'thread_image_path'         =>  $this->thread_image_path,
            'temp_thread_image_path'    =>  $this->temp_thread_image_path,
            // 'temp_image_url'            =>  $this->temp_image_url,
            'remote_image_url'            =>  $this->remote_image_url,
            'image_path'                =>  $this->image_path,
            'image_path_pixel_color'    =>  $this->image_path_pixel_color,
            'image_description'         =>  $this->image_description,
            'amazon_product_url'         =>  $this->amazon_product_url,
            'cno'                       =>  $this->cno,
            'age_restriction'           =>  $this->age_restriction,
            'anonymous'                 =>  $this->anonymous,
            'location'                  =>  $this->location,
            'formatted_address'         =>  $this->formatted_address,
            'favorites_count'           =>  $this->favorite_count,
            'visits'                    =>  $this->visits,
            'is_favorited'              =>  $this->is_favorited,
            'is_published'              =>  $this->is_published,
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

            // $this->whenLoaded('channel',  new ChannelResource($this->channel)),

            'creator'                   => $this->whenLoaded('creator', function(){
                return new UserResource($this->creator);
            }),
            // 'creator'                   =>  new UserResource($this->creator),
            'tags'                      =>  TagResource::collection($this->whenLoaded('tags')),
            'emojis'                    =>  EmojiResource::collection($this->whenLoaded('emojis')),
            'is_owner'                  => $this->is_owner,
            // 'word_count'                =>  $this->word_count,
            // 'tag_names'                =>  $this->tag_names,
            // 'tag_ids'                =>  $this->tag_ids,
            // 'emoji_ids'                =>  $this->emoji_ids,

        ];
    }
}
