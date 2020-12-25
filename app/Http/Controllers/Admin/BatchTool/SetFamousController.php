<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class SetFamousController extends Controller
{
    public function body(Request $request){
        $request->validate([
            'set_famous_body' =>  'required',
            'set_famous_body_category' =>  'required'
        ],[
            'set_famous_body.required' => 'The body field is required.',
            'set_famous_body_category.required' => 'The famous field is required.'
        ]);

        Thread::where('body', 'LIKE', "%{$request->set_famous_body}%")->chunk(100, function($threads) use($request){
            foreach($threads as $thread){
                $thread->update(['cno'=>strtoupper($request->set_famous_body_category)]);
            }
        });

        return response(['success'=> true, 'message'=> 'Threads famous set successfully'], Response::HTTP_ACCEPTED);
    }

    public function title(Request $request){
        $request->validate([
            'set_famous_title' =>  'required',
            'set_famous_title_category' =>  'required'
        ],[
            'set_famous_title.required' => 'The title field is required.',
            'set_famous_title_category.required' => 'The famous field is required.'
        ]);

        Thread::where('title', 'LIKE', "%{$request->set_famous_title}%")->chunk(100, function($threads) use($request){
            foreach($threads as $thread){
                $thread->update(['cno'=>strtoupper($request->set_famous_title_category)]);
            }
        });

        return response(['success'=> true, 'message'=> 'Threads famous set successfully'], Response::HTTP_ACCEPTED);
    }


    public function tag(Request $request){
        $request->validate([
            'set_famous_tag' =>  ['required','exists:tags,name'],
            'set_famous_tag_category' =>  ['required']
        ],[
            'set_famous_tag.required' => 'The tag field is required.',
            'set_famous_tag.exists' => 'Tag was not found.',
            'set_famous_tag_category.required' => 'The famous field is required.'
        ]);
        $tag = Tag::where('name', strtolower($request->set_famous_tag))->first();

        $tag->threads()->chunk(100, function($threads) use($request){
            foreach($threads as $thread){
                $thread->update(['cno'=>strtoupper($request->set_famous_tag_category)]);
            }
        });

        return response(['success'=> true, 'message'=> 'Threads famous set successfully'], Response::HTTP_ACCEPTED);
    }

}
