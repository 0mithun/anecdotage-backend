<?php

namespace App\Http\Controllers\Channel;

use App\Models\Channel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
    public function search(Request $request){
        // return $request->name;
        $channels = Channel::where('name', 'like', $request->name. '%')->get(['name','id','slug']);
        return $channels;
    }
}
