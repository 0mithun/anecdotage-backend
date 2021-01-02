<?php
namespace App\Repositories\Eloquent;

use App\Models\Room;
use App\Repositories\Contracts\IRoom;

class RoomRepository extends BaseRepository implements IRoom
{

    public function model()
    {
        return Room::class;
    }

}
