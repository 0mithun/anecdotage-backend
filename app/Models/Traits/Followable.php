<?php

namespace App\Models\Traits;

use App\Models\User;
use App\Models\Follow;

trait Followable{

    protected static function bootFollowable(){
        static::deleting(function($model){
            $model->follows->each->delete();
        });
    }


    public function follows(){
        return $this->morphMany(Follow::class,'followable');
    }

    public function follow(){
        $attributes = ['user_id' => auth()->id()];
        $this->follows()->create($attributes);
    }

    public function unfollow(){
        $attributes = ['user_id' => auth()->id()];
        $this->follows()->where($attributes)->get()->each->delete();
    }

    public function isFollow(){
        $attributes = ['user_id' => auth()->id()];
        return auth()->check() && (bool) $this->follows->where('user_id', auth()->id())->count();
    }

    public function getIsFollowAttribute(){
        return $this->isFollow();
    }


    public function getfollowersAttribute(){
        $followersId = $this->follows()->pluck('user_id')->toArray();
        return User::whereIn('id', $followersId)->get();
    }

    public function getFollowersCountAttribute(){
        return $this->follows->count();
    }
}
