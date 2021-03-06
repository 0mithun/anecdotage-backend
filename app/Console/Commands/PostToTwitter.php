<?php

namespace App\Console\Commands;

use App\Models\Thread;
use Illuminate\Console\Command;
use App\Notifications\ThreadPostTwitter;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class PostToTwitter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share:twitter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share threads to twitter';

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
            } catch (CouldNotSendNotification $th) {
                //throw $th;
            }
        });
    }
}
