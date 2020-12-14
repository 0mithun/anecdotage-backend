<?php

namespace App\Http\Controllers\Thread;

use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{

    protected $threads ;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }

   public function report(Request $request, Thread $thread){

        // if($thread->is_reported){
        //     return response(['success'=> false,'message'=>'You are already report this item'], Response::HTTP_NOT_ACCEPTABLE);
        // }

        $thread->report($request->only(['reason','type','contact']));
        return response(['success'=> true,'message'=>'Thread Report Successfully'], Response::HTTP_CREATED);
   }
}
