<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SlideSettingResource extends JsonResource
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
            'site_name' =>  $this->site_name,
            'site_title' =>  $this->site_title,
            'default_email_address' =>  $this->default_email_address,
            'site_logo'=>  $this->site_logo,
            'site_favicon'=>  $this->site_favicon,
            'logo_path'     => $this->logo_path,
            'favicon_path'     => $this->favicon_path,
            'footer_copyright_text'=>  $this->footer_copyright_text,
            'seo_meta_title'=>  $this->seo_meta_title,
            'seo_meta_description'=>  $this->seo_meta_description,
            'seo_meta_keyword'=>  $this->seo_meta_keyword,
            'social_facebook'=>  $this->social_facebook,
            'social_twitter' =>  $this->social_twitter,
            'social_instagram'=>  $this->social_instagram,
            'social_linkedin'=>  $this->social_linkedin,
            'google_analytics'=>  $this->google_analytics,
            'facebook_pixels'=>  $this->facebook_pixels,
            'faq'       => $this->faq,
            'about'=>  $this->about,
            'privacypolicy'=>  $this->privacypolicy
        ];
    }
}
