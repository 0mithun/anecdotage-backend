<?php

namespace App\Http\Controllers\Search;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITag;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class ThreadController extends Controller
{
    protected $threads;
    protected $tags;

    protected $filter = [];
    protected $index = 0;


    public function __construct(IThread $threads, ITag $tags)
    {
        $this->threads = $threads;
        $this->tags = $tags;
    }


    public function index(Request $request){
        $this->validate($request, [
            'q' => ['required']
        ]);

        $results = $this->search($request);
        return $results;

        $threadIds =  $results->pluck('id')->toArray();
        $tags =  $results->pluck('tag_ids');

        $tagIds = $tags->collapse()->unique()->toArray();
        $tags = $this->tags->findWhereIn('id', $tagIds);



        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis','channel']),
        ])->findWhereInSameOrderPaginate('id', $threadIds);

        return response(['tags' => TagResource::collection($tags)->response()->getData(true), 'threads'=> ThreadResource::collection($threads)->response()->getData(true)]);

    }



    /**
     * Build query for filtr cno
     *
     * @param string $cno
     * @return void
     */
    protected function buildCnoQuery($cno){
        $validCno = ['c','n','o'];
        $splitCno = explode(',', $cno);

        if(count($splitCno)> 0){
            foreach($splitCno as $cno){
                if(in_array( strtolower($cno), $validCno)){
                    $this->filter[$this->index]['terms']['cno'][] = strtolower($cno);
                }
            }
        }

        $this->index = count($this->filter);
    }


    /**
     * Build Query for filter ages
     *
     * @param string $ages
     * @return void
     */
    protected function buildAgesQuery($ages){
        $validAges = [18,13,0];
        $splitAges = explode(',', $ages);

        if(count($splitAges)> 0){
            foreach($splitAges as $age){
                if(in_array( (int) $age , $validAges)){
                    $this->filter[$this->index]['terms']['age_restriction'][] = $age;
                }
            }
        }
        $this->index = count($this->filter);
    }



    /**
     * Build query for filter length
     *
     * @param string $length
     * @return void
     */
    protected function buildLengthQuery($length){
        $validLength = ['sort', 'medium','long'];
        $splitLength = explode(',', $length);

        //sort min(0) max(100)
        //medium min(100) max(300)
        //long min(300)
        $minArray = [];
        $maxArray = [];

        if(count($splitLength)> 0){
            rsort($splitLength);
            foreach($splitLength as $length){
                if(in_array( strtolower($length) , $validLength)){
                    if(strtolower($length) == 'sort'){
                        $minArray[] = 0;
                        $maxArray[] = 100;
                    }else if(strtolower($length) == 'medium'){
                        $minArray[] = 100;
                        $maxArray[] = 300;
                    }else if(strtolower($length) == 'long'){
                        $minArray[] = 300;
                        $maxArray = [];
                    }
                }
            }
        }

        if(count($minArray)> 0){
            // $filter[$index]['range']['visits']['gte']=  min($minArray);
            $this->filter[$this->index]['range']['word_count']['gte']=  min($minArray);
        }
        if(count($maxArray) > 0){
            // $filter[$index]['range']['visits']['lte']=  max($maxArray);
            $this->filter[$this->index]['range']['word_count']['lte']=  max($maxArray);
        }
        $this->index = count($this->filter);
    }

     /**
     * Build query for filter length
     *
     * @param string $tags
     * @return void
     */
    public function buildTagsQuery($tags){
        $splitTags = explode(',', $tags);

        if(count($splitTags)> 0){
            $tag_ids = [];
            foreach($splitTags as $tag){
                $tag_ids[]= (int) preg_replace('/[^0-9]/', '', $tag);
            }
            $this->filter[$this->index]['terms']['tag_ids']  = $tag_ids;
        }

        $this->index = count($this->filter);
    }



    /**
     * Build search params
     *
     * @param string $query
     * @return mixed
     */
    protected function buildParams($query){
       $params = [
            "bool" => [
                'must' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ["title^3", "body^3", 'tag_names']
                    ],
                ],

                'filter' =>   $this->filter
            ]

        ];

        return $params;
    }



    /**
     * Search items
     *
     * @param Request $request
     * @return mixed
     */
    protected function search(Request $request){
        if($request->has('cno') && $request->cno != null ){
            $this->buildCnoQuery($request->cno);
          }

          if($request->has('ages') && $request->ages != null ){
              $this->buildAgesQuery($request->ages);
          }

          if($request->has('length') && $request->length != null ){
              $this->buildLengthQuery($request->length);
          }

          if($request->has('tags') && $request->tags != null ){
              $this->buildTagsQuery($request->tags);
          }

          $params = $this->buildParams(request()->get('q'));

          $search = Thread::searchByQuery($params);
          $results = Thread::customSearch($params, null, null, $search->totalHits());

          return $results;
    }
}
