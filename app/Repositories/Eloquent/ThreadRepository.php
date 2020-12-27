<?php
namespace App\Repositories\Eloquent;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IThread;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class ThreadRepository extends BaseRepository implements IThread
{

    public function model()
    {
        return Thread::class;
    }

    public function orderByRaw(string $statement){
        $this->model->getQuery()->orders = [];
        $this->model->orderByRaw($statement);

        return $this;
    }

    public function whereLike(string $solumn, string $value){
        $this->model->where('body', 'LIKE', "%{$value}%");

        return $this;
    }

    public function searchLocation(Request $request){
        $query = (new $this->model)->newQuery();

        $lat = $request->lat;
        $lng =  $request->lng;
        $distance = request( 'radius' ) ?? 1000;
        $distance *= 1609.34;

        $query->whereNotNull('location');

        if($lat && $lng){
            $point = new Point($lat, $lng);
            $query->distanceSphereExcludingSelf('location', $point, $distance);
            $query->orderByDistance('location', $point, 'asc');
        }
        return $query->get();
    }
}
