<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPrivacy extends Model
{
    protected $table = 'user_privacies';

    protected $fillable = [
        'user_id',
        'see_my_profiles',
        'see_my_threads',
        'see_my_favorites',
        'see_my_friends',
        'send_me_message',
        'thread_create_share_facebook',
        'thread_create_share_twitter',
        'anyone_share_my_thread_facebook',
        'anyone_share_my_thread_twitter',
        'restricted_13',
        'restricted_18'
    ];

    protected $casts = [
        'thread_create_share_facebook'  =>  'boolean',
        'thread_create_share_twitter'  =>  'boolean',
        'anyone_share_my_thread_facebook'  =>  'boolean',
        'anyone_share_my_thread_twitter'  =>  'boolean',
        'restricted_13'  =>  'boolean',
        'restricted_18'  =>  'boolean',
    ];
}
