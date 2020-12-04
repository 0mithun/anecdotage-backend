<?php

namespace App\Models;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $fillable = [
        'name',
        'photo',
        'description',
    ];
    protected $appends = ['profileAvatarPath', 'followType'];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        // static::created(function ($tag) {
        //     TagImageProcessing::dispatch($tag);
        // });
    }

    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'thread_tag', 'tag_id', 'thread_id');
    }

    // public function follows()
    // {
    //     return $this->morphMany('App\Follows', 'followable');
    // }

    public function getNameAttribute($name)
    {
        return ucfirst($name);
    }

    public function setNameAttribute($value){
        $this->attributes['name'] = strtolower($value);
    }


    public function getProfileAvatarPathAttribute($avatar)
    {
        $avatar = $this->photo == '' ? 'images/avatars/default.png' : $this->photo;
        //https://www.maxpixel.net/static/photo/1x/Geometric-Rectangles-Background-Shapes-Pattern-4973341.jpg
        // $avatar = 'images/avatars/default.png';

        return asset($avatar);
    }

    public function getFollowTypeAttribute($type)
    {
        return 'tag';
    }
}
