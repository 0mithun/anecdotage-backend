<?php

namespace App\Http\Controllers\Thread;

use Carbon\Carbon;
use App\Models\ThreadView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;

class TrendingController extends Controller
{


    public function index(){
        $treanding = ThreadView::where('created_at','>=',Carbon::now()->subHours(24))
        ->select('thread_id', DB::raw('count(*) as total'))
        ->orderBy('total','desc')
        ->groupBy('thread_id')
        // ->limit(3)
        ->get()
        ->load('thread')
        ->pluck('thread');

        return  ThreadResource::collection($treanding);
    }

}
