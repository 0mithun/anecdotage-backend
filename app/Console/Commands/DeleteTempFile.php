<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteTempFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:temp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete teporary file from storage';

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
        $files = Storage::disk('public')->files('download/temp/threads');

        foreach($files as $file){
            Storage::disk('public')->delete($file);
            dump($file);
        }


    }
}
