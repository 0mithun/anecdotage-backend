<?php

namespace App\Console\Commands;

use App\Models\Thread;
use Illuminate\Console\Command;
use App\Notifications\ThreadPostTwitter;
use App\Notifications\ThreadPostFacebook;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class ShareSocial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:social';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share thread to social networks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $threads  = Thread::where('age_restriction',0)->get();
        $threads->each(function($thread){
            try {
                $thread ->notify(new ThreadPostTwitter);
                $thread ->notify(new ThreadPostFacebook);
            } catch (CouldNotSendNotification $th) {
                //throw $th;
            }
        });
    }
}
