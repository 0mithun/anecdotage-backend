<?php

namespace App\Http\Controllers\Search;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITag;
use App\Http\Resources\ThreadResource;
use App\Models\Traits\Filterable;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class ThreadController extends Controller
{
    use Filterable;

    public function __construct(IThread $threads, ITag $tags)
    {
        $this->threads = $threads;
        $this->tags = $tags;


    }


    public function index(Request $request)
    {
        $this->validate($request, [
            'q' => ['required']
        ]);

        $results = $this->search($request);

        // if($results->count()==0){
        //     return 'not ok';

        // }

        $pagination = $this->buildPaginate($results);

        $threadIds =  $results->pluck('id')->toArray();

        $tags =  $results->pluck('tag_ids');
        $tagIds = $tags->collapse()->unique()->toArray();
        $tags = $this->tags->findWhereIn('id', $tagIds);


        if($results->count()==0){
            $threads = $this->threads->withCriteria([
                new EagerLoad(['emojis', 'channel']),
            ])->findWhereIn('id', $threadIds);
        }

        else{
            $threads = $this->threads->withCriteria([
                new EagerLoad(['emojis', 'channel']),
            ])->findWhereInSameOrder('id', $threadIds)->get();
        }



    //    return $threads;


        return response(['tags' => TagResource::collection($tags)->response()->getData(true), 'threads' => ['data' => ThreadResource::collection($threads), 'meta' => $pagination]]);

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
                    'multi_match' => [
                        "type"=> "cross_fields",
                        "minimum_should_match"=> "100%",
                        'query' => $query,
                        'fields' => ["title^4", "body^3", 'tag_names'],
                        //  "analyzer"=> "keyword",
                        // 'sort'  =>  [
                        //     // 'visits'    => 'desc'
                        // ]
                    ],
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
    protected function search(Request $request)
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

        if ($request->has('emojis') && $request->emojis != null) {
            $this->buildEmojisQuery($request->emojis);
        }

        $params = $this->buildParams(request()->get('q'));

        $sort =   $this->sortSearch($request);

        // $search = Thread::searchByQuery($params);
        // $tags = Thread::customSearch($params, null, ['tag_names', 'tag_ids'], $search->totalHits(),  null, $sort);

        $offset = (request('page',1) - 1) * $this->per_page;

        $results = Thread::customSearch($params, null, ['id', 'tag_ids'], $this->per_page,  $offset, $sort)->paginate($this->per_page);


        return $results;
    }


}
