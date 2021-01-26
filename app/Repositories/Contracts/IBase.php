<?php

namespace App\Repositories\Contracts;

interface IBase
{
    public function all();
    public function find($id);
    public function findWhere($column, $value);
    public function findWhereArray(array $criteria);
    public function findWhereIn($column, array $data);
    public function findWhereInPaginate($column, array $data, $perPage = 10);
    public function findWhereFirst($column, $value);
    public function paginate($perPage = 10);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

    public function orderBy($column, $sort = 'ASC');

    public function toSql();

    public function findWhereInSameOrderPaginate($column, array $data, $perPage = 10);

    public function select(array $column = ['*']);
}
