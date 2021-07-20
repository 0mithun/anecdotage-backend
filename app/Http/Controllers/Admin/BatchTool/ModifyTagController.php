<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Channel;
use Symfony\Component\HttpFoundation\Response;

class ModifyTagController extends Controller
{
    /**
     * Rename Tag
     */

    public function rename(Request $request){
        $this->validate($request, [
            'old_tag_name' =>  ['required',],
            'new_tag_name' =>  ['required',]
        ],[
            'old_tag_name.exists'   => 'Old tag was not found'
        ]);



        $old_tag = Tag::where('slug', str_slug($request->old_tag_name))->first();
        if(!$old_tag){
            return response()->json(['errors'=>['old_tag_name'=>['Old tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newTag = Tag::where('slug', str_slug($request->new_tag_name))->first();

        if($newTag){
            $old_tag->threads()->chunk(100, function($threads) use($old_tag, $newTag){
                foreach($threads as $thread){
                    $thread->tags()->detach($old_tag->id);
                    $thread->tags()->detach($newTag->id);
                    $thread->tags()->attach($newTag->id);
                }
            });
            $old_tag->delete();
        }else{
            $old_tag->update(['name' => strtolower($request->new_tag_name),'slug'=> str_slug($request->new_tag_name)]);
        }

        return \response(['success'=> true, 'message'=>'Tag rename successfully'], Response::HTTP_ACCEPTED);
    }


    /**
     * Delete tag
     */

    public function delete(Request $request){
        $request->validate([
            'delete_tag_name' =>  ['required',],
        ],[
            'delete_tag_name.required'  =>  'The tag name field is required.',
        ]);



        $tag = Tag::where('slug', str_slug($request->delete_tag_name))->first();
        if(!$tag){
            return response()->json(['errors'=>['tag'=>['Delete tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tag->threads()->chunk(100, function($threads) use($tag){
            foreach($threads as $thread){
                $thread->tags()->detach($tag->id);
            }
        });
        $tag->delete();

        return \response(['success'=> true, 'message'=>'Tag delete successfully'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove tag if thread body has text
     */

    public function removeBodyTag(Request $request){

        $request->validate([
            'body' =>  ['required',],
            'tag' =>  ['required'],
        ],[
            'tag.required'  =>  'The  remove tag field is required.',
            'body.required'  =>  'The body field is required.',
        ]);

        $tag = Tag::where('slug', str_slug($request->tag))->first();
        if(!$tag){
            return response()->json(['errors'=>['tag'=>['Remove tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Thread::where('body', 'LIKE', "%{$request->body}%")->chunk(100, function($threads) use($tag) {
            foreach($threads as $thread){
                $thread->tags()->detach($tag->id);
            }
        });


        return \response(['success'=> true, 'message'=>'Tag remove successfully'], Response::HTTP_NO_CONTENT);
    }



    /**
     * Remove tag if thread has tag
     */

    public function removeTagTag(Request $request){
        $request->validate([
            'find_tag' =>  ['required',],
            'remove_tag' =>  ['required'],
        ]);

        $find_tag = Tag::where('slug', str_slug($request->find_tag))->first();
        $remove_tag = Tag::where('slug', str_slug($request->remove_tag))->first();
        if(!$find_tag){
            return response()->json(['errors'=>['find_tag'=>['Find tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if(!$remove_tag){
            return response()->json(['errors'=>['remove_tag'=>['Remove tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $find_tag->threads()->chunk(100, function($threads) use($remove_tag){
            foreach($threads as $thread){
                $thread->tags()->detach($remove_tag->id);
            }
        });


        return \response(['success'=> true, 'message'=>'Tag remove successfully'], Response::HTTP_NO_CONTENT);
    }
    /**
     * Change channel if thread has tag
     */

    public function changeChannel(Request $request){
        $request->validate([
            'tag' =>  ['required',],
            'channel' =>  ['required'],
        ]);

        $tag = Tag::where('slug', str_slug($request->tag))->first();
        $channel = Channel::where('slug', str_slug($request->channel))->first();
        if(!$tag){
            return response()->json(['errors'=>['tag'=>['Tag was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if(!$channel){
            return response()->json(['errors'=>['channel'=>['Channel was not found']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tag->threads()->chunk(100, function($threads) use($channel){
            foreach($threads as $thread){
                $thread->update([
                    'channel_id'    =>  $channel->id,
                ]);
            }
        });


        return \response(['success'=> true, 'message'=>'Channel change successfully'], Response::HTTP_NO_CONTENT);
    }
}
