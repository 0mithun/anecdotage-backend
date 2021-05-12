<?php

namespace App\Http\Controllers\Thread;

use Carbon\Carbon;
use App\Models\Thread;
use App\Models\ThreadView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrendingThreadResource;

class TrendingController extends Controller
{

    public function index()
    {
        $treandingsId = ThreadView::where('created_at', '>=', Carbon::now()->subHours(24))
            ->groupBy('thread_id')
            ->pluck('thread_id')->toArray();

        // $threads = Thread::whereIn('id', $treandingsId)->orderBy('visits', 'DESC')->limit(20)->get();


        $threads = Thread::whereIn('id', $treandingsId)->select([
            "id",
            "title",
            "slug",
            "image_path",
            "image_path_pixel_color",
            "like_count",
            "dislike_count",
            "created_at",
            "updated_at",
        ])->orderBy('visits', 'DESC')->limit(20)->get();

        return  TrendingThreadResource::collection($threads);
    }
}
