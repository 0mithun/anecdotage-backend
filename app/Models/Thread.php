<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Emoji;
use App\Models\Reply;
use App\Models\Channel;
use App\Models\ThreadView;
use App\Filters\ThreadFilter;
use App\Models\Traits\Likeable;
use App\Models\Traits\Reportable;
use App\Models\ThreadSubscription;
use App\Models\Traits\Favoritable;
use App\Models\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Notifications\Notifiable;

class Thread extends Model
{
    use Notifiable, Favoritable, Likeable, Reportable, SpatialTrait, SearchableTrait;




    protected $spatialFields = [
        'location',
    ];

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
        'user_id', 'channel_id', 'slug', 'title', 'body', 'summary', 'source', 'main_subject', 'image_path', 'image_path_pixel_color', 'image_description', 'temp_image_url', 'temp_image_description', 'image_saved', 'cno', 'age_restriction', 'anonymous', 'formatted_address', 'location', 'is_published', 'visits', 'favorite_count', 'like_count', 'dislike_count', 'slide_body', 'slide_image_pos', 'slide_color_bg', 'slide_color_0', 'slide_color_1', 'slide_color_2'

    ];

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
            $thread->removeFromIndex();

            $thread->replies->each->delete();
            $thread->subscriptions->each->delete();

            $thread->tags()->sync([]);
            $thread->emojis()->sync([]);
            $thread->views->each->delete();

            Storage::disk('public')->delete($thread->image_path);
        });

        static::created(function ($thread) {
            $thread->addToIndex();
            $thread->update(['slug' => str_slug(strip_tags( $thread->title))]);

        });

        static::updated(function ($thread) {
            $thread->updateIndex();
        });

        static::addGlobalScope(new ThreadFilter);
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
     * Access the body attribute.
     *
     * @param  string $body
     * @return string
     */
    public function getBodyAttribute($body)
    {   $pattern =  '<p>&nbsp;</p>';
        $body = str_replace($pattern, '', $body);
        $body =  html_entity_decode($body);
        return $body;
    }
    /**
     * Access the body attribute.
     *
     * @param  string $body
     * @return string
     */
    public function setBodyAttribute($value)
    {
        $pattern =  '<p>&nbsp;</p>';
        $body = str_replace($pattern, '', $value);

        $pattern = '/<a(.*?)<\/a>/i';
        $body = preg_replace_callback($pattern, function($match){
            return '<em><a href="https://www.amazon.com/s?k='.trim($match[1]).'&linkCode=ur2&tag=anecdotage01-20">'.trim($match[1]).'</a></em>';
        }, $body);


        $pattern = '@<i>\s*?<a(.*?)>(.*?)<\/a>\s*?<\/i>@i';
        $body = preg_replace_callback($pattern, function($match){
            return '<em><a href="https://www.amazon.com/s?k='.trim($match[2]).'&linkCode=ur2&tag=anecdotage01-20">'.trim($match[2]).'</a></em>';
        }, $body);


        $pattern = '/<i>(.*?)<\/i>/i';
        $body = preg_replace_callback($pattern, function($match){
            return '<em><a href="https://www.amazon.com/s?k='.trim($match[1]).'&linkCode=ur2&tag=anecdotage01-20">'.trim($match[1]).'</a></em>';
        }, $body);


        $this->attributes['body'] = $body;
    }

    /**
     * Access the title attribute.
     *
     * @param  string $title
     * @return string
     */
    public function getTitleAttribute($title)
    {
        return (html_entity_decode($title));
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



    public function getExcerptAttribute()
    {
        $limit = 120;
        $body = strip_tags($this->body);
        $body = preg_replace('/\s+/', ' ', $this->body);

        $splitBody = explode(" ", $body);
        if(count($splitBody)>$limit){
            $splitBody = array_slice($splitBody,0, $limit);

            $body =  implode(" ", $splitBody);
            $body = $body." <strong>...</strong>";
        }

        return $body;
    }

    public function threadImagePath()
    {
        if ($this->image_path != '') {
            if (preg_match("/http/i", $this->image_path)) {
                return $this->image_path;
            }
            // else if (preg_match("/download/i", $this->image_path)) {
            //     return asset($this->image_path);
            // }
            return asset('storage/' . $this->image_path);
        } else {
            return 'https://www.maxpixel.net/static/photo/1x/Geometric-Rectangles-Background-Shapes-Pattern-4973341.jpg';
        }
    }

    public function tempThreadImagePath(){
         if ($this->image_path != '' || $this->image_path != null) {
            if (preg_match("/http/i", $this->image_path)) {
                return $this->image_path;
            }

            return '';
        }

        return '';
    }

    public function getThreadImagePathAttribute()
    {
        return $this->threadImagePath();
    }

    public function getTempThreadImagePathAttribute(){
        return $this->tempThreadImagePath();
    }

    public function getRemoteImageUrlAttribute(){
         if ($this->image_path != '' || $this->image_path != null) {
            if (preg_match("/http/i", $this->image_path)) {
                return $this->image_path;
            }

            return $this->temp_image_url;
        }
    }

    public function getImageDescriptionAttribute($value){

         return trim(html_entity_decode($value)." ".$this->amazon_product_url);

        //image_description
        $description = '';
        if (preg_match("%wikimedia.org%i", $this->image_path)) {
            // return $this->imageDescriptionReplace($value);
            return $value;
        }

        if($this->old_image_description == null || $this->old_image_description == ''){
            //  return $this->imageDescriptionReplace($value);
            return 'sama';
        }

        $description = $this->imageDescriptionReplace($this->old_image_description);

        return html_entity_decode($description);
    }

    public function imageDescriptionReplace($description){
        $pattern = '/<a(.*?)>(.*?)<\/a>/i';
        $description = preg_replace_callback($pattern, function($match){
            return ' <a'.$match[1].'>Buy it here</a>';
        }, $description);


        $pattern = '/(class="(.*)?" )/i';
        $description = preg_replace_callback($pattern, function($match){
            return ' class="btn btn-sm btn-secondary" ';
        }, $description);

        return $description;
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
     * A thread can have many tags
     *
     * @return mixed
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tag', 'thread_id', 'tag_id');
    }


    /**
     * A thread can have many emojis
     *
     * @return mixed
     */
    public function emojis()
    {
        return $this->belongsToMany(Emoji::class, 'thread_emoji', 'thread_id', 'emoji_id');
    }

    public function getPointsAttribute()
    {
        return ($this->like_count - $this->dislike_count);
    }


    public function views()
    {
        return $this->hasMany(ThreadView::class);
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }

    public function getUserEmojiVoteAttribute()
    {
        if (!auth()->check()) {
            return null;
        }
        return $this->emojis()->where('user_id', auth()->id())->first();
    }

    public function getIsOwnerAttribute()
    {
        return (bool) auth()->check() && auth()->id() === $this->user_id;
    }

    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->body));
    }
    public function getTagNamesAttribute()
    {
        return  $this->tags()->pluck('name');
    }
    public function getTagIdsAttribute()
    {
        return  $this->tags()->pluck('id');
    }
    public function getEmojiIdsAttribute()
    {
        return $this->emojis()->distinct('id')->get()->pluck('id')->toArray();
    }
}
