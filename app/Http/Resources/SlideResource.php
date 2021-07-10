<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SlideResource extends JsonResource
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
            'thread_image_path'         =>  $this->thread_image_path,
            'image_path'                =>  $this->image_path,
            'image_path_pixel_color'    =>  $this->image_path_pixel_color,
            'image_description'         =>  $this->image_description,
            'full_image_description'    =>  $this->full_image_description,

            'age_restriction'           =>  $this->age_restriction,


            // //Slide Fields
            'slide_body'                =>  $this->slide_body,
            'style_slide_body'          =>  $this->style_slide_body,
            'slide_body_length'         =>  $this->slide_body_length,
            'slide_color_0'             =>  $this->slide_color_0,
            'slide_color_1'             =>  $this->slide_color_1,
            'slide_color_2'             =>  $this->slide_color_2,
            'slide_color_bg'            =>  $this->slide_color_bg,
            'slide_image_pos'           =>  $this->slide_image_pos,
            'slide_logo_color'           =>  $this->slide_logo_color,

            //currently unused
            'thread_slide_image_path'   =>  $this->thread_slide_image_path,





            'slide_screenshot_path'   =>  $this->slide_screenshot_path,
            'ready'                     =>  $this->slide_ready,
            'seo_meta_description'      =>  $this->slide_meta_description,
            'seo_meta_keyword'          =>  $this->meta_keyword,

        ];
    }
}
