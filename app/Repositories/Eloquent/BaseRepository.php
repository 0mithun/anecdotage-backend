<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;
use Illuminate\Pagination\Paginator;

abstract class BaseRepository implements IBase, ICriteria
{

    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function all()
    {
        return $this->model->get();
    }

    public function find($id)
    {
        $result = $this->model->findOrFail($id);
        return $result;
    }

    public function findWhere($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereArray(array $criteria){
          return  $this->model->where($criteria)->get();
    }

    public function findWhereIn($column, array $data)
    {
        return $this->model->whereIn($column, $data)->get();
    }

    public function findWhereInPaginate($column, array $data, $perPage = 10)
    {
        $this->currentPage();
        return $this->model->whereIn($column, $data)->paginate($perPage);
    }


    public function findWhereFirst($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 10)
    {   $this->currentPage();
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        $result = $this->model->create($data);
        return $result;
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }


    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);

        foreach($criteria as $criterion){
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }



    protected function getModelClass()
    {
        if( !method_exists($this, 'model'))
        {
            throw new ModelNotDefined();
        }

        return app()->make($this->model());

    }


    protected function currentPage(){
        Paginator::currentPageResolver(function(){
            if(request()->has('page') && request()->page != null){
                return request()->page;
            }else{
                return 1;
            }
        });
    }

    public function orderBy($column , $sort = 'ASC'){
        $this->model->orderBy($column, $sort);
        return $this;
    }

    public function toSql(){
        return $this->model->toSql();
    }

}
