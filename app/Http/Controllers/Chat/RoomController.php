<?php

namespace App\Http\Controllers\Chat;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\RoomResource;
use App\Repositories\Contracts\IRoom;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class RoomController extends Controller
{
    protected $rooms;

    public function __construct(IRoom $rooms)
    {
        $this->rooms = $rooms;
    }

    public function index(){

        $rooms = $this->rooms->withCriteria([
            new EagerLoad(['users','messages'])
        ])->all();

        return RoomResource::collection($rooms);

    }

    public function show(Request $request, Room $room){
        $messages = $room->messages()->whereNull('parent_id')->get();

        return ChatMessageResource::collection($messages);
    }
}
