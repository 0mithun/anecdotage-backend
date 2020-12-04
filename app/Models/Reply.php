<?php

namespace App\Models;

use App\Models\User;
use App\Models\Thread;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $fillable = [
        'thread_id','user_id','body','parent_id',
    ];


    public static function boot(){
        parent::boot();

        static::created(function($reply){
            if(!$reply->thread->IsSubscribed()){
                $reply->thread->subscribe();
            }
        });
    }


    public function childs(){
        return $this->hasMany(Reply::class,'parent_id','id');
    }


    public function parent(){
        return $this->belongsTo(Reply::class, 'parent_id');
    }


    public function getRepliesCountAttribute(){
        return $this->childs->count();
    }


    /**
     * A reply has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A reply belongs to a thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Fetch all mentioned users within the reply's body.
     *
     * @return array
     */
    public function mentionedUsers()
    {
        preg_match_all('/@(?<=@)[a-zA-Z]+\s[a-zA-Z]+/', $this->body, $matches);
        $name = substr($matches[0],1);

        return $name;
    }

    /**
     * Determine the path to the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    /**
     * Set the body attribute.
     *
     * @param string $body
     */
    public function setBodyAttribute($body)
    {
        $line = preg_replace_callback(
//            '/@([\w\-]+)/',
            '/@(?<=@)[a-zA-Z]+\s[a-zA-Z]+/',

            function ($matches) {
                $name = substr($matches[0],1);
                 $user = User::where( 'name', $name)->first();
                if($user){
                    return "<a href=\"/profiles/".$user->username."\">".$matches[0]."</a>";
                }else{
                    return $matches[0];
                }


            },
            $body
        );

        $this->attributes['body'] = $line;
    }

    /**
     * Access the body attribute.
     *
     * @param  string $body
     * @return string
     */
    public function getBodyAttribute($body)
    {
        // return \Purify::clean($body);
        return html_entity_decode($body);
    }


    // public function getIsReportedAttribute()
    // {
    //     $report = DB::table('reports')
    //         ->where('user_id', auth()->id())
    //         ->where('reported_id', $this->id)
    //         ->where('reported_type','App\Reply')
    //         ->first();
    //     ;
    //     if($report){
    //         return true;
    //     }else{
    //         return false;
    //     }

    // }
}
