<?php
namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;

class ThreadSort implements ICriterion
{
    protected $valid_sort;

    public function __construct(array $valid_sort =['visits','favorite_count'] )
    {
        $this->valid_sort = $valid_sort;
    }


    public function apply($model)
    {
        if(request('sort_by') && request('sort_by') !=''){
            $sort = request('sort_by');
            if($sort == 'top_rated'){
               return $model->orderByRaw('like_count - dislike_count DESC');
            }else if(in_array($sort, $this->valid_sort)){
               return $model->orderBy($sort, 'desc');
            }
            return $model->orderByRaw('like_count - dislike_count DESC');
        }else{
           return $model->orderByRaw('like_count - dislike_count DESC');
        }
    }
}
