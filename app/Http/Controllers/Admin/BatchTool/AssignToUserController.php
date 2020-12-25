<?php

namespace App\Http\Controllers\Admin\BatchTool;

use App\Models\Tag;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssignToUserController extends Controller
{
    public function body(Request $request){
        $request->validate([
            'assign_user_body' =>  ['required'],
            'assign_user_body_username' => ['required','exists:users,username']
        ],[
            'assign_user_body.required' => 'The body field is required.',
            'assign_user_body_username.required' => 'The username field is required.',
            'assign_user_body_username.exists' => 'User not found.',
        ]);

        $user = User::where('username',$request->assign_user_body_username)->first();
       Thread::where('body', 'LIKE', "%{$request->assign_user_body}%")
        ->chunk(100, function($threads) use($user){
            foreach($threads as $thread){
                $thread->user_id = $user->id;
                $thread->save();
            }
        });

    }

    public function title(Request $request){
        $request->validate([
            'assign_user_title' =>  ['required'],
            'assign_user_title_username' =>  ['required','exists:users,username']
        ],[
            'assign_user_title.required' => 'The title field is required.',
            'assign_user_title_username.required' => 'The username field is required.',
            'assign_user_title_username.exists' => 'User not found.',
        ]);
        $user = User::where('username',$request->assign_user_title_username)->first();

        Thread::where('title', 'LIKE', "%{$request->assign_user_title}%")
            ->chunk(100, function($threads) use($user){
                foreach($threads as $thread){
                    $thread->user_id = $user->id;
                    $thread->save();
                }
            });

    }

    public function tag(Request $request){
        $request->validate([
            'assign_user_tag' =>  ['required','exists:tags,name'],
            'assign_user_tag_username' =>  ['required','exists:users,username']
        ],[
            'assign_user_tag.required' => 'The tag is required.',
            'assign_user_tag.exists' => 'Tag was not found.',
            'assign_user_tag_username.required' => 'The username field is required.',
            'assign_user_tag_username.exists' => 'User not found.',
        ]);
        $user = User::where('username',$request->assign_user_tag_username)->first();

        $tag = Tag::where('name', strtolower($request->assign_user_tag))->first();
        $tag->threads()->chunk(100, function($threads) use($user){
            foreach($threads as $thread){
                $thread->user_id = $user->id;
                $thread->save();
            }
        });

    }

}
