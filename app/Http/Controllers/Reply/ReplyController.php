<?php

namespace App\Http\Controllers\Reply;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Thread;
use App\Repositories\Contracts\IReply;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReplyController extends Controller
{

    protected $replies;

    public function __construct(IReply $replies)
    {
        $this->replies = $replies;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Thread $thread)
    {
        $replies = $this->replies->withCriteria([
            new EagerLoad(['owner','parent'])
        ])->findWhere('thread_id', $thread->id);

        return response(ReplyResource::collection($replies));
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
            'body'  => ['required']
        ]);

        $reply = $this->replies->create($request->only(['body','parent_id']) + ['thread_id'=> $thread->id, 'user_id'=> auth()->id()]);

        return response(new ReplyResource($reply), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show( Thread $thread, Reply $reply)
    {
        return new ReplyResource($reply);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread, Reply $reply)
    {
        $this->validate($request, [
            'body'      => ['required'],
        ]);

        $reply = $this->replies->update($reply->id, $request->only(['body']));
        return response(new ReplyResource($reply), Response::HTTP_ACCEPTED);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, Reply $reply)
    {
        $this->replies->delete($reply->id);

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function childs(Thread $thread, Reply $reply){

        $replies = $this->replies->findWhere('parent_id',$reply->id);

        return response(ReplyResource::collection($replies));
    }
}
