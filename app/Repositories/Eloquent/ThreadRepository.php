<?php
namespace App\Repositories\Eloquent;

use App\Models\Thread;
use App\Repositories\Contracts\IThread;

class ThreadRepository extends BaseRepository implements IThread
{

    public function model()
    {
        return Thread::class;
    }

    public function orderByRaw(string $statement){
        $this->model->getQuery()->orders = [];
        $this->model->orderByRaw($statement);

        return $this;
    }

    public function whereLike(string $solumn, string $value){
        $this->model->where('body', 'LIKE', "%{$value}%");

        return $this;
    }
}
