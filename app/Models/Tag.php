<?php

namespace App\Models;

use App\Models\Thread;
use App\Jobs\TagImageProcessing;
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

        static::created(function ($tag) {
            TagImageProcessing::dispatch($tag);
        });

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

    public function getDescriptionAttribute($value)
    {
        //<a class="btn btn-xs btn-primary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=12th century&linkCode=ur2&tag=anecdotage01-20">Shop for 12th century</a>

        $splitDescription = explode('<a class="btn btn-xs btn-primary" href="http://www.amazon.com',$value);
        $shopText = $splitDescription[0];
        $shopText =  $shopText . '<a class="btn btn-sm btn-secondary" href="http://www.amazon.com/gp/search?ie=UTF8&camp=1789&creative=9325&index=aps&keywords=' . $this->name . '&linkCode=ur2&tag=anecdotage01-20">Buy it here</a>';
        return $shopText;
    }

    public function getPhotoUrlAttribute()
    {
         if ($this->photo != '') {
            if (preg_match("/http/i", $this->photo)) {
                return $this->photo;
            } else if (preg_match("/download/i", $this->photo)) {
                return asset($this->photo);
            }
            return asset('storage/' . $this->photo);
        } else {
            return 'https://www.maxpixel.net/static/photo/1x/Geometric-Rectangles-Background-Shapes-Pattern-4973341.jpg';
        }


    }

    public function getFollowTypeAttribute($type)
    {
        return 'tag';
    }
}
