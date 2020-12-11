<?php

namespace App\Http\Controllers\Channel;

use App\Models\Channel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;

class ChannelController extends Controller
{
    public function index(){
        $channels = Channel::all();

        return response(ChannelResource::collection($channels));
    }


    public function search(Request $request){
        // return $request->name;
        $channels = Channel::where('name', 'like', $request->name. '%')->get(['name','id','slug']);
        return $channels;
    }
}
