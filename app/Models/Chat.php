<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'from','to','message','friend_message','reply_id','reply_message','seen_at'
    ];


    protected $casts = [
        'friend_message'    => 'boolean',
    ];

    protected $dates = ['seen_at'];

    public function user(){
        return $this->belongsTo(User::class,'from');
    }
}
