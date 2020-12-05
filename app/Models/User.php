<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Thread;
use App\Models\Traits\Followable;
use App\Models\Traits\FriendShipTrait;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
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


    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'available_to_hire' => 'boolean'
    ];

    protected static function boot() {
        parent::boot();
        // static::created( function ( $user ) {
        //     $user->usersetting()->create( [
        //         'mention_notify_anecdotage'                     => 1,
        //         'mention_notify_email'                          => 0,
        //         'mention_notify_facebook'                       => 0,
        //         'new_thread_posted_notify_anecdotage'           => 1,
        //         'new_thread_posted_notify_email'                => 0,
        //         'new_thread_posted_notify_facebook'             => 0,
        //         'receive_daily_random_thread_notify_anecdotage' => 1,
        //         'receive_daily_random_thread_notify_email'      => 0,
        //         'receive_daily_random_thread_notify_email'      => 0,
        //     ] );

        //     $user->userprivacy()->create( [
        //         'see_my_threads'                  => 3,
        //         'see_my_favorites'                => 3,
        //         'see_my_friends'                  => 3,

        //         'send_me_message'                 => 2,

        //         'thread_create_share_facebook'    => 0,
        //         'thread_create_share_twitter'     => 0,

        //         'anyone_share_my_thread_facebook' => 1,
        //         'anyone_share_my_thread_twitter'  => 1,
        //     ] );

        // } );
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
        return auth()->id() == 1;
    }

    public function getIsAdminAttribute() {
        return $this->isAdmin();
    }


    // public function usersetting() {
    //     return $this->hasOne( Usersetting::class );
    // }

    // public function userprivacy() {
    //     return $this->hasOne( Userprivacy::class );
    // }

    // public function chat() {
    //     return $this->hasMany( Chat::class, 'from' );
    // }

    // public function userban() {
    //     return $this->hasOne( Userban::class );
    // }

    // public function isBanned() {
    //     $userBan = $this->userban;
    //     if ( $userBan ) {
    //         if ( $userBan->ban_type == 1 ) {
    //             return true;
    //         }

    //         $ban_expire_on = $userBan->ban_expire_on;
    //         $now = Carbon::now();
    //         if ( $ban_expire_on->lte( $now ) ) {
    //             // $this->userban->delete();
    //             return false;
    //         } else {
    //             return true;
    //         }

    //     }

    //     return false;
    // }

    // public function getIsBannedAttribute() {
    //     return $this->isBanned();
    // }



    public function getFollowTypeAttribute( $type ) {
        return 'user';
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
