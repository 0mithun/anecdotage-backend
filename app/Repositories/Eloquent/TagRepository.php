<?php
namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\ITag;

class TagRepository extends BaseRepository implements ITag
{

    public function model()
    {
        return Tag::class;
    }


}
