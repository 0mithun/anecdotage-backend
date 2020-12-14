<?php

namespace App\Http\Controllers\Reply;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ReplyResource;
use App\Repositories\Contracts\IReply;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\Criteria\EagerLoad;

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
        // $replies = $this->replies->withCriteria([
        //     new EagerLoad(['owner'])
        // ])->findWhere('thread_id', $thread->id);


        $replies = $this->replies->withCriteria([
            new EagerLoad(['owner'])
        ])->findWhereArray(['thread_id'=> $thread->id, 'parent_id'=> null]);

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

        return response(new ReplyResource($reply->load('owner')), Response::HTTP_CREATED);
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
        Gate::authorize('update-reply', $reply);

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
        Gate::authorize('update-reply', $reply);
        $this->replies->delete($reply->id);

        return response(null, Response::HTTP_NO_CONTENT);
    }


    public function childs(Thread $thread, Reply $reply){

        $replies = $this->replies->withCriteria([
            new EagerLoad(['owner'])
        ])->findWhere('parent_id',$reply->id);

        return response(ReplyResource::collection($replies));
    }
}
