<?php

namespace App\Http\Controllers\Emoji;

use App\Models\Emoji;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmojiResource;
use App\Models\Thread;
use App\Repositories\Contracts\IEmoji;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class EmojiController extends Controller
{

    protected $emojis;

    public function __construct(IEmoji $emojis){
        $this->emojis = $emojis;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emojis = $this->emojis->all();

        return EmojiResource::collection($emojis);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Emoji  $emoji
     * @return \Illuminate\Http\Response
     */
    public function show(Emoji $emoji)
    {
        $emoji = $this->emojis->withCriteria([
            new EagerLoad('threads')
        ])->find($emoji->id);



        return new EmojiResource($emoji);
    }



}
