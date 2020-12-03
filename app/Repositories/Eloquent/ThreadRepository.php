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


}
