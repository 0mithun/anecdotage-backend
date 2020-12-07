<?php
namespace App\Repositories\Eloquent;
use App\Models\Userban;
use App\Repositories\Contracts\IUserBan;

class UserBanRepository extends BaseRepository implements IUserBan
{

    public function model()
    {
       return Userban::class;
    }


}
