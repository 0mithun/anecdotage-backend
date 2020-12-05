<?php

namespace App\Repositories\Contracts;

interface IBase
{
    public function all();
    public function find($id);
    public function findWhere($column, $value);
    public function findWhereIn($column, array $data);
    public function findWhereInPaginate($column, array $data, $perPage = 10);
    public function findWhereFirst($column, $value);
    public function paginate($perPage = 10);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

}
