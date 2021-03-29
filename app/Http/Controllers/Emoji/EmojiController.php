<?php

namespace App\Http\Controllers\Emoji;

use App\Models\Emoji;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmojiResource;
use App\Http\Resources\ThreadResource;
use App\Models\Traits\Filterable;
use App\Repositories\Contracts\IEmoji;
use App\Repositories\Contracts\ITag;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class EmojiController extends Controller
{
    use Filterable;

    protected $emojis;
    protected $threads;
    protected $tags;
    protected $emoji;

    public function __construct(IEmoji $emojis, IThread $threads, ITag $tags)
    {
        $this->emojis = $emojis;
        $this->threads = $threads;
        $this->tags = $tags;
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
    public function show(Emoji $emoji, Request $request)
    {
        $this->emoji = $emoji;
        // $threadsId =  $emoji->threads->pluck('id')->toArray();

        // $threads = $this->threads->withCriteria([
        //     new EagerLoad(['channel', 'emojis']),
        // ])->findWhereInPaginate('id', $threadsId);

        // $emojiResponse =  (new EmojiResource($emoji));
        // $threads =  $emoji->threads()->with(['channel', 'emojis'])->paginate();
        // return response(['emoji' => $emojiResponse->response()->getData(true), 'threads' => ThreadResource::collection($threads)->response()->getData(true)]);



        $results = $this->filterThread($request);

        $pagination = $this->buildPaginate($results);

        $threadIds =  $results->pluck('id')->toArray();

        $tags =  $results->pluck('tag_ids');
        $tagIds = $tags->collapse()->unique()->toArray();
        $tags = $this->tags->findWhereIn('id', $tagIds);

        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->findWhereIn('id', $threadIds);

        return response(['emoji'=> (new EmojiResource($emoji))->response()->getData(true),  'tags' => TagResource::collection($tags)->response()->getData(true), 'threads' => ['data' => ThreadResource::collection($threads), 'meta' => $pagination]]);



    }



    /**
     * Build search params
     *
     * @param string $query
     * @return mixed
     */
    protected function buildParams($query)
    {
        $params = [
            "bool" => [
                'must' => [
                    // 'multi_match' => [
                    //     'query' => ' ',
                    //     'fields' => ["title"],
                    //     // 'sort'  =>  [
                    //     //     // 'visits'    => 'desc'
                    //     // ]
                    // ],
                    'exists'    =>  [
                         'field' => ["title"],
                    ]
                ],

                'filter' =>   $this->filter
            ],
        ];

        return $params;
    }



    /**
     * Search items
     *
     * @param Request $request
     * @return mixed
     */
    protected function filterThread(Request $request)
    {
        if ($request->has('cno') && $request->cno != null) {
            $this->buildCnoQuery($request->cno);
        }

        $this->buildAgesQuery($request);


        if ($request->has('length') && $request->length != null) {
            $this->buildLengthQuery($request->length);
        }

        if ($request->has('tags') && $request->tags != null) {
            $this->buildTagsQuery($request->tags);
        }


        $this->buildEmojisQuery($request);

        $params = $this->buildParams(request()->get('q'));

        $sort =   $this->sortSearch($request);

        // $search = Thread::searchByQuery($params);
        // $tags = Thread::customSearch($params, null, ['tag_names', 'tag_ids'], $search->totalHits(),  null, $sort);

        $offset = (request('page',1) - 1) * $this->per_page;

        $results = Thread::customSearch($params, null, ['id', 'tag_ids'], $this->per_page,  $offset, $sort)->paginate($this->per_page);


        return $results;
    }

      /**
     * Build query for filter emojis
     *
     * @param string $emojis
     * @return void
     */
    public function buildEmojisQuery(Request $request)
    {
        $emoji_ids = [];
         $emoji_ids[]  = $this->emoji->id;
         if ($request->has('emojis') && $request->emojis != null) {
             $splitEmojis = explode(',', $request->emojis);

             if (count($splitEmojis) > 0) {

                 foreach ($splitEmojis as $emoji) {
                     $emoji_ids[] = (int) preg_replace('/[^0-9]/', '', $emoji);
                 }
            }
        }
        $this->filter[$this->index]['terms']['emoji_ids']  = $emoji_ids;




        $this->index = count($this->filter);
    }
}
