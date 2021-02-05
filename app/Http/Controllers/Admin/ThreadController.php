<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\WikiImageProcess;
use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function update(Request $request, Thread $thread)
    {
        $title = $request->title;

        $split_title = explode("*", $title);
        if (count($split_title) > 0 && $split_title[0] != '') {
            $keyword = $split_title[0];
            dispatch(new WikiImageProcess($keyword, $thread));
        }
    }
}
