<?php

namespace App\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_name',
        'site_title',
        'default_email_address',
        'site_logo',
        'site_favicon',
        'footer_copyright_text',
        'seo_meta_title',
        'seo_meta_description',
        'seo_meta_keyword',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_linkedin',
        'social_patreon',
        'google_analytics',
        'facebook_pixels',
        'faq',
        'tos',
        'privacypolicy',
    ];

    public function getLogoPathAttribute(){
        if ($this->site_logo != '') {
            return asset('storage/'.$this->site_logo);
        } else {
            return asset('images/logo.jpg');
        }
    }


    public function getFaviconPathAttribute(){
        if ($this->site_favicon != '') {
            return asset('storage/'.$this->site_favicon);
        } else {
            return asset('images/favicon.png');
        }
    }
}
