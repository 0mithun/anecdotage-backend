<?php

namespace App\Models;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;

class ThreadView extends Model
{
    protected $table = 'thread_views';

    protected $fillable = ['thread_id'];



    public function thread(){
        return $this->belongsTo(Thread::class);
    }
}
