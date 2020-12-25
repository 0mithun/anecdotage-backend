<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ThreadAddTagController extends Controller
{
    public function body(Request $request){
        $request->validate([
            'add_tag_body' =>  ['required'],
            'add_tag_body_tag_name' =>  ['required','exists:tags,name']
        ],[
            'add_tag_body.required' => 'The body field is required.',
            'add_tag_body_tag_name.exists' => 'Tag was not found.',
            'add_tag_body_tag_name.required' => 'The tag name field is required.',
        ]);

        $tag = Tag::where('name', strtolower($request->add_tag_body_tag_name))->first();
        if(!$tag){
            $tag = Tag::create(['name' => strtolower($request->add_tag_body_tag_name),'slug'=> str_slug($request->add_tag_body_tag_name)]);
        }

       Thread::where('body', 'LIKE', "%{$request->add_tag_body}%")->chunk(100, function($threads) use($tag){
            foreach($threads as $thread){
                $thread->tags()->syncWithoutDetaching([$tag->id]);
            }
        });

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully']. Response::HTTP_CREATED);
    }


    public function title(Request $request){
        $request->validate([
            'add_tag_title' =>  ['required'],
            'add_tag_title_tag_name' =>  ['required','exists:tags,name']
        ],[
            'add_tag_title.required' => 'The title field is required.',
            'add_tag_title_tag_name.exists' => 'Tag was not found.',
            'add_tag_title_tag_name.required' => 'The tag name field is required.'
        ]);


        $tag = Tag::where('name', strtolower($request->add_tag_title_tag_name))->first();
        if(!$tag){
            $tag = Tag::create(['name' => strtolower($request->add_tag_title_tag_name), 'slug'=> \str_slug($request->add_tag_title_tag_name)]);
        }
        Thread::where('title', 'LIKE', "%{$request->add_tag_title}%")->chunk(100, function($threads) use($tag){
            foreach($threads as $thread){
                $thread->tags()->syncWithoutDetaching([$tag->id]);
            }
        });

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
    }


    public function tag(Request $request){
        $request->validate([
            'add_tag_with_tag' =>  ['required','exists:tags,name'],
            'add_tag_with_tag_tag_name' =>  ['required']
        ],[
            'add_tag_with_tag.exists' => 'Tag was not found.',
            'add_tag_with_tag.required' => 'The old tag name field is required.',
            'add_tag_with_tag_tag_name.required' => 'The tag name field is required.'
        ]);

        $old_tag = Tag::where('name', strtolower($request->add_tag_with_tag))->first();

        $new_tag = Tag::where('name', strtolower($request->add_tag_with_tag_tag_name))->first();
        if(!$new_tag){
            $new_tag = Tag::create(['name' => strtolower($request->add_tag_with_tag_tag_name), 'slug'=> \str_slug($request->add_tag_with_tag_tag_name)]);
        }
        $old_tag->threads()->chunk(100, function($threads) use($new_tag){
            foreach($threads as $thread){
                $thread->tags()->syncWithoutDetaching([$new_tag->id]);
            }
        });

        return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
    }
}
