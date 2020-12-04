<?php
namespace App\Repositories\Eloquent;

use App\Models\Reply;
use App\Repositories\Contracts\IReply;

class ReplyRepository extends BaseRepository implements IReply
{

    public function model()
    {
        return Reply::class;
    }


}
