<?php

namespace App\Models;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{
    protected $fillable = ['name'];


    public function threads(){
        return $this->belongsToMany(Thread::class,'thread_emoji','emoji_id','thread_id');
    }

    public function getPhotoUrlAttribute(){
        return asset('images/emojis/'.$this->name.'.png');
    }



}
