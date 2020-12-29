<?php

namespace App\Http\Controllers\Search;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IThread;

class ThreadController extends Controller
{
    protected $threads;
    protected $filter = [];
    protected $index = 0;


    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }




    public function index(Request $request){
        $this->validate($request, [
            'query' => ['required']
        ]);

        $results = $this->search($request);


        $ids =  $results->pluck('id');
        $tags =  $results->pluck('tag_ids');

        return $tags->collapse()->unique();
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
                    $this->filter[$this->index]['terms']['cno'][] = $cno;
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
                    if($length == 'sort'){
                        $minArray[] = 0;
                        $maxArray[] = 100;
                    }else if($length == 'medium'){
                        $minArray[] = 100;
                        $maxArray[] = 300;
                    }else if($length == 'long'){
                        $minArray[] = 300;
                        $maxArray = [];
                    }
                }
            }
        }

        if(count($minArray)> 0){
            // $filter[$index]['range']['word_count']['gte']=  min($minArray);
            $this->filter[$this->index]['range']['visits']['gte']=  min($minArray);
        }
        if(count($maxArray) > 0){
            // $filter[$index]['range']['word_count']['lte']=  max($maxArray);
            $this->filter[$this->index]['range']['visits']['lte']=  max($maxArray);
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

          $params = $this->buildParams(request()->get('query'));

          $search = Thread::searchByQuery($params);
          $results = Thread::customSearch($params, null, null, $search->totalHits());

          return $results;
    }
}