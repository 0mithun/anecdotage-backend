<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->reported_type == 'App\Models\Thread') {
            $reporte_item = [
                'title' =>  $this->reported->title,
                'slug' =>  $this->reported->slug,
                'channel' => new ChannelResource($this->reported->channel),
            ];
        }
        return [
            'id'    =>  $this->id,
            'reason'    =>  $this->reason,
            'report_type'    =>  $this->report_type,
            'contact'    =>  $this->contact,
            'reported_type'    =>  class_basename($this->reported_type),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'report_user'   =>  [
                'id'    =>  $this->user->id,
                'name'    =>  $this->user->name,
                'username'    =>  $this->user->username,
                'email'    =>  $this->user->email,
                'photo_url'    =>  $this->user->photo_url,
            ],
            'reported_item' => $reporte_item
        ];
    }
}
