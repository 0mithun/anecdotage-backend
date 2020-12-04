<?php

namespace App\Http\Controllers\Thread;

use App\Http\Controllers\Controller;
use App\Models\Emoji;
use App\Models\Thread;
use App\Repositories\Contracts\IEmoji;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmojiController extends Controller
{

    protected $emojis;


    public function __construct(IEmoji $emojis)
    {
        $this->emojis = $emojis;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $this->emojis->removeVote($thread);
            $this->emojis->addVote($thread, $emoji);
        }else{
            $this->emojis->addVote($thread, $emoji);
        }

        return \response(['success'=> true], Response::HTTP_ACCEPTED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Emoji  $emoji
     * @return \Illuminate\Http\Response
     */
    public function show(Emoji $emoji)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Emoji  $emoji
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Emoji $emoji)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Emoji  $emoji
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, Emoji $emoji)
    {
        $this->emojis->removeVote($thread);
        return \response(['success'=> true], Response::HTTP_NO_CONTENT);
    }
}
