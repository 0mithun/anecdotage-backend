<?php

namespace App\Http\Controllers\Thread;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\Channel;
use Illuminate\Http\Request;
use Spatie\Geocoder\Geocoder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Thread\ThreadCreateRequest;
use App\Http\Requests\Thread\ThreadUpdateRequest;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class ThreadController extends Controller
{
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $threads = Thread::paginate();
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis','channel']),
        ])->paginate();
        return  ThreadResource::collection($threads);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadCreateRequest $request)
    {
        $data = $request->only(['title','body','source','main_subject','cno','age_restriction','anonymous','channel_id','famous',]);
        if ($request->location != null) {
            $location = $this->getGeocodeing($request->location);
            if ($location['accuracy'] != 'result_not_found') {
                $data['location'] = new Point($location['lat'], $location['lng']);
            }
        }
        $thread = $this->threads->create($data + ['user_id'=>auth()->id()]);

        $this->attachTags($request, $thread);
        return response(new ThreadResource($thread), Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        $thread->views()->create([]);
        $thread->update(['visits' => $thread->visits  + 1]);



        $thread = $this->threads->withCriteria([
            new EagerLoad(['tags','creator','emojis','channel'])
        ])->find($thread->id);
        return new ThreadResource($thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadUpdateRequest $request, Thread $thread)
    {
        $data = $request->only(['title','body','source','main_subject','cno','age_restriction','anonymous','channel_id','famous',]);
        if($request->has('title') && auth()->user()->is_admin){
            $data['slug'] = str_slug(strip_tags( $request->title));
        }
        $thread = $this->threads->update($thread->id, $data);
        $this->attachTags($request, $thread);
        return response(new ThreadResource($thread), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        Gate::authorize('edit-thread', $thread);
        $this->threads->delete($thread->id);
        return response(null, Response::HTTP_NO_CONTENT);
    }

      /**
     * Get lat, lng with thread location
     */

    public function getGeocodeing($address)
    {
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));
        $geocoder->setCountry(config('geocoder.country', 'US'));
        return $geocoder->getCoordinatesForAddress($address);
    }


    /**
     * Attacg & Save Thread Tags
     */

    public function attachTags($request, $thread)
    {
        $tags = [];
        if ($request->has('tags') && $request->tags != null) {
            $tags = explode(',', $request->tags);
        }

        if ($request->has('channel') && $request->channel != null) {
            $channel = json_decode($request->channel);
            $type = gettype($channel);
            if($type == 'string'){
                $findChannel = Channel::where('name', $channel)->first();
                if($findChannel){
                    if (!in_array(str_slug($findChannel->name), $tags)) {
                        $tags[] = str_slug($findChannel->name);
                    }
                }
            }else{
                if (!in_array(str_slug($channel->name), $tags)) {
                    $tags[] = str_slug($channel->name);
                }
            }
        }

        if ($request->has('main_subject') && $request->main_subject != null) {
            if (!in_array(str_slug($request->main_subject), $tags)) {
                $tags[] = str_slug($request->main_subject);
            }
        }


        $tag_ids = [];
        foreach ($tags as $tag) {
            $searchTag = Tag::where('slug', str_slug($tag))->first();

            if ($searchTag) {
                $tag_ids[] = $searchTag->id;
            } else {
                if ($tag != 'null') {
                    $newTag = Tag::create(['name' => $tag,'slug'=>str_slug($tag)]);
                    $tag_ids[] = $newTag->id;
                }
            }
        }

        $thread->tags()->sync($tag_ids);
    }
}
