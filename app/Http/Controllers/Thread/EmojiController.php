<?php

namespace App\Http\Controllers\Thread;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmojiResource;
use App\Models\Emoji;
use App\Models\Thread;
use App\Repositories\Contracts\IEmoji;
use App\Repositories\Contracts\IThread;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmojiController extends Controller
{

    protected $emojis;
    protected $threads;

    public function __construct(IEmoji $emojis, IThread $threads)
    {
        $this->emojis = $emojis;
        $this->threads = $threads;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Thread $thread)
    {
        $this->validate($request, [
            'emoji_id'  =>  ['required']
        ]);

        $emoji = $this->emojis->find($request->emoji_id);
        if($this->emojis->isVote($thread)){
            $this->emojis->removeVote($thread, $emoji);
            $this->emojis->addVote($thread, $emoji);
        }else{
            $this->emojis->addVote($thread, $emoji);
        }
        $thread->updateIndex();

        return \response(['success'=> true], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Emoji  $emoji
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, Emoji $emoji)
    {
        $this->emojis->removeVote($thread, $emoji);
        $thread->updateIndex();

        return \response(['success'=> true], Response::HTTP_NO_CONTENT);
    }


    /**
     * Get user vote type
     * @param \App\Models\Thread $thread
     * @return \Illuminate\Http\Resources
     */

    public function userVoteType(Thread $thread){
        if($emoji = $thread->emojis()->where('user_id', auth()->id())->first()){
            return response(new EmojiResource($emoji));
        }
        return response(null, Response::HTTP_NOT_FOUND);
    }
}
