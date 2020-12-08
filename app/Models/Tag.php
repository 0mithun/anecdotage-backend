<?php

namespace App\Models;

use App\Models\Thread;
use App\Models\Traits\Followable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tag extends Model
{

    use Followable;

    protected $fillable = [
        'name',
        'slug',
        'photo',
        'description',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // protected $appends = ['profileAvatarPath', 'followType'];

    // public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        // static::created(function ($tag) {
        //     TagImageProcessing::dispatch($tag);
        // });

        static::deleting(function($tag){
            $tag->threads()->sync([]);
            Storage::disk('public')->delete($tag->photo);
        });
    }

    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'thread_tag', 'tag_id', 'thread_id');
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
