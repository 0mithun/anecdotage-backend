<?php
namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public static function bootLikeable()
    {
        static::deleting(function($model){
            $model->removeLikes();
        });
    }

    // delete likes when model is being deleted
    public function removeLikes()
    {
        if($this->likes()->count()){
            $this->likes()->delete();
        }
    }


    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if(! auth()->check()) return;

        // check if the current user has already liked the model
        if($this->isLikedByUser()){
            $this->unlike();
            $this->update(['like_count'=>  $this->like_count - 1]);
        }else if($this->isDisLikedByUser()){
            $this->unlike();
            $this->likes()->create(['user_id' => auth()->id(),'vote_type'=>'UP']);
            $this->update(['like_count'=> $this->like_count + 1, 'dislike_count'=>  $this->dislike_count -1]);
        }else{
            $this->likes()->create(['user_id' => auth()->id(),'vote_type'=>'UP']);
            $this->update(['like_count'=> $this->like_count + 1]);
        }

    }

    public function dislike()
    {
        if(! auth()->check()) return;

        // check if the current user has already disliked the model
        if($this->isDisLikedByUser()){
            $this->unlike();
            $this->update(['dislike_count'=>  $this->dislike_count - 1]);
        }else if($this->isLikedByUser()){
            $this->unlike();
            $this->likes()->create(['user_id' => auth()->id(),'vote_type'=>'DOWN']);
            $this->update(['like_count'=> $this->like_count - 1, 'dislike_count'=>  $this->dislike_count + 1]);
        }else{
            $this->likes()->create(['user_id' => auth()->id(),'vote_type'=>'DOWN']);
            $this->update(['dislike_count'=> $this->dislike_count + 1]);
        }
    }



    public function unlike()
    {
        if(! auth()->check()) return;

        $this->likes()
            ->where('user_id', auth()
            ->id())->delete();
    }

    public function isLikedByUser()
    {
        return (bool)$this->likes()
                ->where('user_id',auth()->id())
                ->where('vote_type','UP')
                ->count();
    }

    public function isDisLikedByUser()
    {
        return (bool)$this->likes()
                ->where('user_id', auth()->id())
                ->where('vote_type','DOWN')
                ->count();
    }

    public function getIsLikedAttribute(){
        return auth()->check() && $this->isLikedByUser();
    }

    public function getIsDisLikedAttribute(){
        return auth()->check() && $this->isDisLikedByUser();
    }

    // public function getLikesCountAttribute(){
    //     return $this->likes()->where('vote_type','UP')->count();
    // }

    // public function getDisLikesCountAttribute(){
    //     return $this->likes()->where('vote_type','DOWN')->count();
    // }



}
