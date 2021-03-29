<?php


namespace App\Models\Traits;

use Illuminate\Http\Request;

trait Filterable{

    protected $threads;
    protected $tags;

    protected $filter = [];
    protected $index = 0;
    protected $per_page = 10;


    /**
     * Build query for filtr cno
     *
     * @param string $cno
     * @return void
     */
    protected function buildCnoQuery($cno)
    {
        $validCno = ['c', 'n', 'o'];
        $splitCno = explode(',', $cno);

        if (count($splitCno) > 0) {
            foreach ($splitCno as $cno) {
                if (in_array(strtolower($cno), $validCno)) {
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
    protected function buildAgesQuery($request)
    {
        $validAges = [18, 13, 0];
        $ages = [];


        //  $this->filter[$this->index]['terms']['age_restriction'][] = 18;
        if(\auth()->check()){
            if(auth()->id() !==1){
                $privacy = auth()->user()->userprivacy;
                if($privacy->restricted_18 ==1){
                    $validAges = [0,13,18];
                }else if($privacy->restricted_13 == 1){
                    $validAges = [0,13,18];
                }else if($privacy->restricted_18 ==0){
                    $validAges = [0];
                }
            }
        }else{
           $validAges = [0];
        }

        if ($request->has('ages') && $request->ages != null) {
             $splitAges = explode(',', $request->ages);
            if (count($splitAges) > 0) {
                foreach ($splitAges as $age) {
                    if (in_array((int) $age, $validAges)) {
                        $this->filter[$this->index]['terms']['age_restriction'][] = $age;
                    }
                }
            }
        }else{
            foreach ($validAges as $age) {
                $this->filter[$this->index]['terms']['age_restriction'][] = $age;
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
    protected function buildLengthQuery($length)
    {
        $validLength = ['sort', 'medium', 'long'];
        $splitLength = explode(',', $length);

        //sort min(0) max(100)
        //medium min(100) max(300)
        //long min(300)
        $minArray = [];
        $maxArray = [];

        if (count($splitLength) > 0) {
            rsort($splitLength);
            foreach ($splitLength as $length) {
                if (in_array(strtolower($length), $validLength)) {
                    if (strtolower($length) == 'sort') {
                        $minArray[] = 0;
                        $maxArray[] = 100;
                    } else if (strtolower($length) == 'medium') {
                        $minArray[] = 100;
                        $maxArray[] = 300;
                    } else if (strtolower($length) == 'long') {
                        $minArray[] = 300;
                        $maxArray = [];
                    }
                }
            }
        }

        if (count($minArray) > 0) {
            // $filter[$index]['range']['visits']['gte']=  min($minArray);
            $this->filter[$this->index]['range']['word_count']['gte'] =  min($minArray);
        }
        if (count($maxArray) > 0) {
            // $filter[$index]['range']['visits']['lte']=  max($maxArray);
            $this->filter[$this->index]['range']['word_count']['lte'] =  max($maxArray);
        }
        $this->index = count($this->filter);
    }

    /**
     * Build query for filter tags
     *
     * @param string $tags
     * @return void
     */
    public function buildTagsQuery($tags)
    {
        $splitTags = explode(',', $tags);

        if (count($splitTags) > 0) {
            $tag_ids = [];
            foreach ($splitTags as $tag) {
                $tag_ids[] = (int) preg_replace('/[^0-9]/', '', $tag);
            }
            $this->filter[$this->index]['terms']['tag_ids']  = $tag_ids;
        }

        $this->index = count($this->filter);
    }

    /**
     * Build query for filter emojis
     *
     * @param string $emojis
     * @return void
     */
    public function buildEmojisQuery($emojis)
    {
        $splitEmojis = explode(',', $emojis);

        if (count($splitEmojis) > 0) {
            $emoji_ids = [];
            foreach ($splitEmojis as $emoji) {
                $emoji_ids[] = (int) preg_replace('/[^0-9]/', '', $emoji);
            }
            $this->filter[$this->index]['terms']['emoji_ids']  = $emoji_ids;
        }

        $this->index = count($this->filter);
    }




    /**
     * Sort Search results by parameter
     *
     * @param Request $request
     * @return array
     */
    public function sortSearch(Request $request)
    {
        $sort = [];

        if ($request->has('sort_by') && $request->sort_by != null) {
            $sortBy = $request->sort_by;
            if ($sortBy == 'visits') {
                $sort[] =  ['visits' => 'desc'];
            }
            // else if ($sortBy == 'favorite') {
            //     $sort[] =  ['favorite_count' => 'desc'];
            // }
            else if ($sortBy == 'like') {
                $sort[] =  ['like_count' => 'desc'];
            } else if ($sortBy == 'top') {
                $sort[] =  ['points' => 'desc'];
            } else if ($sortBy == 'recent') {
                $sort[] =  ['date' => 'desc'];
            }
        }

        return $sort;
    }


    public function buildPaginate($results)
    {
        // "total":total(),
        // "per_page": perPage(),
        // "current_page": $results->currentPage(),
        // "last_page": lastPage(),
        // "next_page_url": nextPageUrl(),
        // "prev_page_url": previousPageUrl()
        // "from": 21,
        // "to": 30,

        $data = [
            'total'     =>  $results->total(),
            'per_page'     =>  $results->perPage(),
            'current_page'     =>  $results->currentPage(),
            'last_page'     =>  $results->lastPage(),
            'next_page_url'     =>  $results->nextPageUrl(),
            'prev_page_url'     =>  $results->previousPageUrl(),
            'from'     =>  $results->firstItem(),
            'to'     =>  $results->lastItem(),
        ];

        return $data;
    }
}
