<?php

namespace App\Http\Controllers\Emoji;

use App\Models\Emoji;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmojiResource;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IEmoji;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class EmojiController extends Controller
{

    protected $emojis;
    protected $threads;

    public function __construct(IEmoji $emojis, IThread $threads){
        $this->emojis = $emojis;
        $this->threads = $threads;
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
        $threadsId =  $emoji->threads->pluck('id')->toArray();

        $threads = $this->threads->withCriteria([
            new EagerLoad(['channel','emojis']),
       ])->findWhereInPaginate('id',$threadsId );

       $emojiResponse =  (new EmojiResource($emoji))->additional([
            'data'  => [

            ]
        ]);

        return response(['emoji' => $emojiResponse->response()->getData(true), 'threads'=> ThreadResource::collection($threads)->response()->getData(true)]);
    }


}
