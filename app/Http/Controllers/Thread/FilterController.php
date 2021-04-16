<?php

namespace App\Http\Controllers\Thread;

use Carbon\Carbon;
use App\Models\Thread;
use App\Models\ThreadView;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class FilterController extends Controller
{

    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function rated()
    {
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->orderByRaw('like_count - dislike_count DESC')->paginate();
        return  ThreadResource::collection($threads);
    }

    public function trending()
    {
        $treandingsId = ThreadView::where('created_at', '>=', Carbon::now()->subHours(24))
            ->groupBy('thread_id')
            ->pluck('thread_id')->toArray();

        $threads = $this->threads->withCriteria([
            new EagerLoad(['channel', 'emojis']),
        ])->findWhereInPaginate('id', $treandingsId);

        return  ThreadResource::collection($threads);
    }

    public function viewed()
    {
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->orderBy('visits', 'desc')->paginate();
        return  ThreadResource::collection($threads);
    }

    public function recent()
    {
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->orderBy('created_at', 'desc')->paginate();
        return  ThreadResource::collection($threads);
    }



    public function video()
    {
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])->whereLike('body', 'https://www.youtube.com/')->orderBy('created_at', 'desc')->paginate();
        return  ThreadResource::collection($threads);
    }

    public function closest()
    {
        if (auth()->check() && auth()->user()->location != null) {
            $auth_user = auth()->user();
            $lat = (float)  $auth_user->location->getLat();
            $lng = (float) $auth_user->location->getLng();
        } else {
            $arr_ip = geoip()->getLocation($_SERVER['REMOTE_ADDR']);
            $lat = (float)  $arr_ip['lat'];
            $lng = (float) $arr_ip['lon'];
        }


        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel'])
        ])->closest($lat, $lng)->paginate();

        return  ThreadResource::collection($threads);
    }
}
