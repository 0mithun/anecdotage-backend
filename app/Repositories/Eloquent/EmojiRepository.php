<?php
namespace App\Repositories\Eloquent;

use App\Models\Emoji;
use App\Models\Thread;
use App\Repositories\Contracts\IEmoji;

class EmojiRepository extends BaseRepository implements IEmoji
{

    public function model()
    {
        return Emoji::class;
    }



    public function isVote(Thread $thread){
        return (bool) $thread->emojis()
            ->where('user_id', auth()->id())
            ->count();
    }

    public function addVote(Thread $thread, Emoji $emoji){
        $thread->emojis()->attach($emoji->id, ['user_id'=> auth()->id()]);
    }

    public function removeVote(Thread $thread){
        $thread->emojis()->where('user_id', auth()->id())->detach();
    }
}
