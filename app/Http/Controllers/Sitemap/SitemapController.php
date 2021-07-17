<?php

namespace App\Http\Controllers\Sitemap;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Thread;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{

    public function tags(){
        $tags = Tag::all(['slug'])->pluck('slug');

        return response($tags, Response::HTTP_OK);
    }

    public function threads(){
        $tags = Thread::select('slug')->where('age_restriction',0)->where(function($q){
            $q->where('body','LIKE',"%2020%")
            ->orWhere('body','LIKE',"%2021%");
        })->pluck('slug');

        return response($tags, Response::HTTP_OK);
    }
}
