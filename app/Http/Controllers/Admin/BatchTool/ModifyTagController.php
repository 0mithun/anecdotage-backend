<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class ModifyTagController extends Controller
{
    public function rename(Request $request){
        $this->validate($request, [
            'old_tag_name' =>  ['required'],
            'new_tag_name' =>  ['required','unique:tags,name']
        ]);
        $tag = Tag::where('name', strtolower($request->old_tag_name))->firstOrFail();
        $tag->update(['name' => strtolower($request->new_tag_name)]);

        return \response(['success'=> true, 'message'=>'Tag rename successfully'], Response::HTTP_ACCEPTED);
    }


    public function delete(Request $request){
        $request->validate([
            'delete_tag_name' =>  'required',
        ],[
            'delete_tag_name.required'  =>  'The tag name field is required.'
        ]);
        $tag = Tag::where('name', strtolower($request->delete_tag_name))->firstOrFail();
        $tag->threads->each(function($thread) use($tag){
            $thread->tags()->detach($tag->id);
        });
        $tag->delete();

        return \response(['success'=> true, 'message'=>'Tag delete successfully'], Response::HTTP_NO_CONTENT);
    }
}
