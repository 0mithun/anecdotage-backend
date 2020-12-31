<?php

namespace App\Repositories\Contracts;

use App\Models\Emoji;
use App\Models\Thread;
use Illuminate\Http\Request;

interface IEmoji
{
    public function isVote(Thread $thread);
    public function addVote(Thread $thread, Emoji $emoji);

    public function removeVote(Thread $thread, Emoji $emoji);
}
