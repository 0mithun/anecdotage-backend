<?php

namespace App\Models;


use App\Models\Tag;
use App\Models\User;
use App\Models\Emoji;
use App\Models\Reply;
use App\Models\Channel;
use App\Models\ThreadView;
use App\Models\Traits\Likeable;
use App\Models\ThreadSubscription;
use App\Models\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Thread extends Model
{
    use Favoritable, Likeable;
      /**
     * Get the route key name.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','channel_id','slug', 'title','body','summary','source','main_subject','image_path','image_path_pixel_color','image_description','image_saved','cno','age_restriction','anonymous','formatted_address','location','is_published','famous','slide_body','slide_image_pos','slide_color_bg','slide_color_0','slide_color_1','slide_color_2'

    ];

    /**
     * The relationships to always eager-load.
     *
     * @var array
     */
    // protected $with = ['creator', 'channel', 'likes', 'tags'];
    // protected $with = ['creator', 'channel', 'likes',];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = ['isSubscribedTo','isReported','isFavorited','excerpt','threadImagePath','path'];
    // protected $appends = ['excerpt',  'threadImagePath', 'imageColor', 'path', 'isLiked', 'isDesliked', 'splitCategory', 'topRated', 'tagNameList'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image_saved'   => 'bool',
        'anonymous'   => 'bool',
        'is_published'   => 'bool',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
            $thread->subscriptions->each->delete();


            Storage::disk('public')->delete($thread->image_path);
        });

        static::created(function ($thread) {
            $thread->update(['slug' => str_slug(strip_tags( $thread->title))]);
        });


        // static::addGlobalScope(new IsPublished);

    }

    public function setTitleAttribute($value){
        $this->attributes['title']  = title_case($value);
    }

     /**
     * Set the proper slug attribute.
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        if (static::whereSlug($slug =  str_slug(strip_tags( $value)))->exists()) {
            $slug = "{$slug}-{$this->id}";
        }

        $this->attributes['slug'] = $slug;
    }


    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        $lower = strtolower($this->channel->slug);

        return "/anecdotes/{$lower}/{$this->slug}";

    }

    public function getPathAttribute()
    {
        return url($this->path());
    }

    /**
     * A thread belongs to a creator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread is assigned a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }


    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    // /**
    //  * Add a reply to the thread.
    //  *
    //  * @param  array $reply
    //  * @return Model
    //  */
    // public function addReply($reply)
    // {
    //     $reply = $this->replies()->create($reply);

    //     event(new ThreadReceivedNewReply($reply));

    //     return $reply;
    // }

    // /**
    //  * Apply all relevant thread filters.
    //  *
    //  * @param  Builder       $query
    //  * @param  ThreadFilters $filters
    //  * @return Builder
    //  */
    // public function scopeFilter($query, ThreadFilters $filters)
    // {
    //     return $filters->apply($query);
    // }


    /**
     * Subscribe a user to the current thread.
     *
     * @param  int|null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id(),
        ]);

        return $this;
    }

    /**
     * Unsubscribe a user from the current thread.
     *
     * @param int|null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * A thread can have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Determine if the current user is subscribed to the thread.
     *
     * @return boolean
     */
    public function IsSubscribed()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }


    /**
     * Determine if the current user is subscribed to the thread.
     *
     * @return boolean
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->IsSubscribed();
    }



    /**
     * Access the body attribute.
     *
     * @param  string $body
     * @return string
     */
    public function getBodyAttribute($body)
    {
        return html_entity_decode($body);
    }

     /**
     * Access the title attribute.
     *
     * @param  string $title
     * @return string
     */
    public function getTitleAttribute($title)
    {
        return title_case(html_entity_decode($title));
    }



    /**
     * Access the source attribute.
     *
     * @param  string $source
     * @return string
     */
    public function getSourceAttribute($source)
    {
        return html_entity_decode($source);
    }

    /**
     *
     * Set the word_count attribute
     * @param string $value
     */


    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tag', 'thread_id', 'tag_id');
    }


    public function emojis()
    {
        return $this->belongsToMany(Emoji::class, 'thread_emoji', 'thread_id', 'emoji_id');
    }

    public function getExcerptAttribute()
    {
        $body = strip_tags($this->body);
        $body = preg_replace('/\s+/', ' ', $this->body);

        $body = substr(strip_tags($body), 0, 250);
        if (strlen($body) <= 250) {
            $body = $body . '<strong>...</strong>';
        }

        return $body;
    }

    public function threadImagePath()
    {
        if ($this->image_path != '') {
            return asset($this->image_path);
        } else {
            return 'https://www.maxpixel.net/static/photo/1x/Geometric-Rectangles-Background-Shapes-Pattern-4973341.jpg';
        }
    }

    public function getThreadImagePathAttribute()
    {
        return $this->threadImagePath();
    }

    // public function splitCategory()
    // {
    //     $categories = $this->category;
    //     if ($categories != null) {
    //         $categories = explode('|', $categories);
    //     }

    //     return $categories;
    // }

    // public function getSplitCategoryAttribute()
    // {
    //     return $this->splitCategory();
    // }


    public function getTopRatedAttribute()
    {
        return ($this->like_count - $this->dislike_count);
    }


    public function views(){
        return $this->hasMany(ThreadView::class);
    }

    public function getViewsCountAttribute(){
        return $this->views()->count();
    }

    public function getIsVotedAttribute(){
        return auth()->check() && (bool) $this->emojis()->where('user_id', auth()->id())->count();
    }
}
