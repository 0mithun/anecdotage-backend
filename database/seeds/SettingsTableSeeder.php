<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'site_name' =>  'Anecdotage',
            'site_title' =>  'Anecdotage',
            'default_email_address' =>  'kakooljay@gmail.com',
            'site_logo'=>  '',
            'site_favicon'=>  '',
            'footer_copyright_text'=>  'Copyright &copy; anecdotage.com',
            'seo_meta_title'=>  '',
            'seo_meta_description'=>  '',
            'seo_meta_keyword'=>  'funny,anecdotes,stories,jokes,facts,trivia,celebrities,famous,people,',
            'social_facebook'=>  '',
            'social_twitter' =>  '',
            'social_instagram'=>  '',
            'social_linkedin'=>  '',
            'google_analytics'=>  '',
            'facebook_pixels'=>  '',
            'faq'       => '',
            'tos'=>  '',
            'privacypolicy'=>  ''
        ];

        $result = Setting::create($settings);
    }
}
