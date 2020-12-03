<?php
namespace App\Repositories\Eloquent;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class UserRepository extends BaseRepository implements IUser
{

    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model
                    ->where('email', $email)
                    ->first();
    }
}
