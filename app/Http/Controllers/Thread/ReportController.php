<?php

namespace App\Http\Controllers\Thread;

use App\Models\User;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\TreadWasReportedEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Contracts\IThread;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\ThreadRestrictionReported;
use App\Notifications\ThreadReportAdminNotifications;

class ReportController extends Controller
{

    protected $threads ;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }

   public function report(Request $request, Thread $thread){

        $this->validate($request, [
            'type'      =>  ['required',Rule::in(['copyright_material','untrue_or_libelous','racist_or_hateful','pornographic','18','13','miscategorized','not_a_story','incorrect','spam'])]
        ]);


        // if($thread->is_reported){
        //     return response(['success'=> false,'message'=>'You are already report this item'], Response::HTTP_NOT_ACCEPTABLE);
        // }

        $thread->report($request->only(['reason','report_type','contact']));
        if(strtolower($request->report_type) == 'copyright_material'){
            $thread->is_published = 0;
            $thread->save();
        }

        $this->sendThraedReportNotification($thread, $request->report_type);
        return response(['success'=> true,'message'=>'Thread Report Successfully'], Response::HTTP_CREATED);
   }


   public function sendThraedReportNotification($thread, $report_type){
        $creator = User::where('id', $thread->user_id)->first();
        $creator->notify( new ThreadRestrictionReported( $thread, $report_type ) );

        $adminUser = User::where('id',1)->first();
        $adminUser->notify( new ThreadReportAdminNotifications( $thread, $report_type ) );

        //Need check later
        Mail::to('anecdotage-reports@gmail.com')
        ->send(new TreadWasReportedEmail($thread, $report_type));
    }




}
