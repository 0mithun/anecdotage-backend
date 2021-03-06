<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'mention_notify_anecdotage',
        'mention_notify_email',
        'mention_notify_facebook',
        'new_thread_posted_notify_anecdotage',
        'new_thread_posted_notify_email',
        'new_thread_posted_notify_facebook',
        'receive_daily_random_thread_notify_anecdotage',
        'receive_daily_random_thread_notify_email',
        'receive_daily_random_thread_notify_facebook',
    ];

    protected $casts = [
        'mention_notify_anecdotage' => 'boolean',
        'mention_notify_email'  => 'boolean',
        'mention_notify_facebook'   => 'boolean',
        'new_thread_posted_notify_anecdotage'   => 'boolean',
        'new_thread_posted_notify_email'    => 'boolean',
        'new_thread_posted_notify_facebook' => 'boolean',
        'receive_daily_random_thread_notify_anecdotage' => 'boolean',
        'receive_daily_random_thread_notify_email'  => 'boolean',
        'receive_daily_random_thread_notify_facebook'   => 'boolean',
    ];
}
