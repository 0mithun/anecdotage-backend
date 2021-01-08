<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'from','to','message','friend_message','parent_id','seen_at'
    ];


    protected $casts = [
        'friend_message'    => 'boolean',
    ];

    protected $dates = ['seen_at'];

    public function user(){
        return $this->belongsTo(User::class,'from');
    }

    public function reply(){
        return $this->hasOne(Chat::class,'parent_id');
    }
    public function parent(){
        return $this->belongsTo(Chat::class,'parent_id');
    }
}
