<?php

namespace App\Http\Controllers\Maps;

use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;

class ThreadsCotnroller extends Controller
{
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }



    public function getAllThread(Request $request) {
        $search = '';
        if($request->has('q') && ($request->q != null || $request->q !='')){
            $search = $request->q;
        }




        if ( $search != '' ) {
            $threads = Thread::
            where(function($query) use($search){
                $query->where( 'title', "LIKE", "%$search%" );
                $query->orWhere( 'body', "LIKE", "%$search%" );
                // $query->orWhere('tag_names','LIKE',"%$search%");
            })
            ->whereNotNull('location')
            ->get()
            ;
        } else {
            $this->validate($request, [
                'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
                'lng' => ['required', 'numeric', 'min:-180', 'max:180']
            ]);
            $threads = $this->threads->searchLocation($request);
        }


        $markers = collect( $threads )->map( function ( $item, $key ) {
            return [
                'position'  => ['lat' => (float) $item->location->getLat(), 'lng' => (float)  $item->location->getLng()],
                'name'      => $item->title,
                'thread_id' => $item->id,
            ];
        } );

        $data = [
            'status'  => 'success',
            'markers' => $markers,
            'results' => ThreadResource::collection($threads),
        ];

        return response( $data, 200 );
    }


    public function search($request){




        // $threadsId = \DB::table('threads')
        // ->selectRaw('id, ( 3959 * acos( cos( radians('.$lat.') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('.$lng.') ) + sin( radians('.$lat.') ) * sin(radians(lat)) ) ) AS distance')
        // ->having('distance','<',$distance)
        // ->orderBy('distance')
        // ->limit(100)
        // // ->lists('id')
        // ->pluck('id')
        // ;
        // return Thread::whereIn('id', $threadsId);
    }

}
