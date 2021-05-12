<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrendingThreadResource extends JsonResource
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
            'slug'                      =>  $this->slug,
            'title'                     =>  $this->title,
            'thread_image_path'         =>  $this->thread_image_path,
            'image_path_pixel_color'    =>  $this->image_path_pixel_color,
            'likes_count'               =>  $this->like_count,
            'dislikes_count'            =>  $this->dislike_count,
            'points'                    =>  $this->points,
            'replies_count'             =>  $this->replies->count(),
        ];
    }
}
