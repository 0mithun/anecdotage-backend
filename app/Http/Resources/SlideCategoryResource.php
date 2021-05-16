<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SlideCategoryResource extends JsonResource
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
            'search_term'       =>  $this->search_term,
            'display_text'      =>  $this->display_text,
            'type'              =>  $this->type,
       ];
    }
}
