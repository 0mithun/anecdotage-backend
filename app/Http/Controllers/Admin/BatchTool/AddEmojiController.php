<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class AddEmojiController extends Controller
{
    public function add(Request $request){
        $request->validate([
            'add_emoji_tag_name' =>  ['required','exists:tags,name'],
            'emoji_name' =>  ['required','exists:emoji,id'],
        ],[
            'add_emoji_tag_name.required'    =>  'The tag name field is required.',
            'add_emoji_tag_name.exists'    =>  'Tag was not found.',
            'emoji_name.required'    =>  'The emoji field is required.',
            'emoji_name.exists'    =>  'Emoji was not found.',
        ]);

        $tag = Tag::where('name', strtolower($request->add_emoji_tag_name))->first();

        $type = $request->emoji_name;
        $userId = 1; //Admin user id = 1
        $tag->threads->each(function($thread) use($userId,$type){
            if($this->isVote($thread->id, $userId)){
                $this->removeVote($thread->id, $userId);
            }
            $thread->emojis()->attach($type,['user_id'=> $userId]);
        });

        return \response(['success'=> true, 'message'=>'Emoji add successfully'], Response::HTTP_ACCEPTED);
    }


     /**
     * Check is thread already vote emoji
     */
    private function isVote($threadId, $userId){
        return (bool) DB::table('thread_emoji')
                    ->where('thread_id', $threadId)
                    ->where('user_id', $userId)
                    ->count();
    }


    /**
     * Remove thread  emoji vote
     */
    private function removeVote($threadId, $userId){
        DB::table('thread_emoji')
        ->where('thread_id', $threadId)
        ->where('user_id', $userId)
        ->delete();
    }
}
