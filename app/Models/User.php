<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\Thread;
use App\Models\Traits\Followable;
use App\Models\ThreadSubscription;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use App\Models\Traits\FriendShipTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SpatialTrait, Followable, FriendShipTrait;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar_path',
        'date_of_birth',
        'about',
        'formatted_address',
        'location',
        'auth_type',
    ];

    protected $spatialFields = [
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getRouteKeyName(){
        return 'username';
    }


    protected $with = ['userban'];

    public function getPhotoUrlAttribute()
    {
        return $this->avatar_path != null ? asset('storage/'.$this->avatar_path) :  'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'available_to_hire' => 'boolean',
    ];


    protected static function boot() {
        parent::boot();
        static::created( function ( $user ) {
            $user->usernotification()->create( [
                'mention_notify_anecdotage'                     => 1,
                'mention_notify_email'                          => 0,
                'mention_notify_facebook'                       => 0,
                'new_thread_posted_notify_anecdotage'           => 1,
                'new_thread_posted_notify_email'                => 0,
                'new_thread_posted_notify_facebook'             => 0,
                'receive_daily_random_thread_notify_anecdotage' => 1,
                'receive_daily_random_thread_notify_email'      => 0,
                'receive_daily_random_thread_notify_email'      => 0,
            ] );

            $user->userprivacy()->create( [
                'see_my_threads'                  => 3,
                'see_my_favorites'                => 3,
                'see_my_friends'                  => 3,

                'send_me_message'                 => 2,

                'thread_create_share_facebook'    => 0,
                'thread_create_share_twitter'     => 0,

                'anyone_share_my_thread_facebook' => 1,
                'anyone_share_my_thread_twitter'  => 1,
            ] );

        } );

        static::deleting(function($user){
            $user->usernotification->delete();
            $user->userprivacy->delete();
            $user->userban->delete();
        });
    }





    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

      /**
     * Fetch all threads that were created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function threads() {
        return $this->hasMany( Thread::class )->latest();
    }





      /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin() {
        return auth()->check() &&  auth()->id() == 1;
    }

    public function getIsAdminAttribute() {
        return $this->isAdmin();
    }


    public function userprivacy() {
        return $this->hasOne( UserPrivacy::class );
    }


    public function usernotification(){
        return $this->hasOne(UserNotification::class);
    }



    public function chat() {
        return $this->hasMany( Chat::class, 'from' );
    }

    public function userban() {
        return $this->hasOne(Userban::class);
    }

    public function isBanned() {
        if ( $userBan = $this->userban ) {
            if ( $userBan->ban_type == 1 ) {
                return true;
            }

            if ( $userBan->ban_expire_on->lte( Carbon::now() ) ) {
                $this->userban->delete();
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    public function getIsBannedAttribute() {
        return $this->isBanned();
    }

    public function getIsFriendAttribute(){
        if(auth()->check()){
            $authenticatedUser = auth()->user();
            return (bool) $authenticatedUser->isFriendWith($this);
        }

        return false;
    }

    public function getIsBlockedAttribute(){
        if(auth()->check()){
            $authenticatedUser = auth()->user();
            return (bool) $authenticatedUser->hasBlocked($this) || $authenticatedUser->isBlockedBy($this);
        }
        return false;
    }




    public function getFollowTypeAttribute( $type ) {
        return 'user';
    }



    /**
     * A thread can have many tags
     *
     * @return mixed
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id');
    }





    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


}
