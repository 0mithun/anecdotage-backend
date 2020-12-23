<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\FriendPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('edit-thread', function($user, $thread){
            return $user->id ===1 || $thread->user_id === $user->id;
        });

        Gate::define('view-own-friendship', function ($user, $model) {
            return $user->id === $model->id;
        });

        Gate::define('view-profile', function($user = null, $model){
            if($user != null){
                if($user->id === $model->id){
                    return true;
                }else if($user->is_admin){
                    return true;
                }else if($model->userprivacy->see_my_profiles==2 && $user->isFriendWith($model)){
                    return true;
                }else if($user->hasBlocked($model) || $user->isBlockedBy($model)){
                    return false;
                }else if($model->userprivacy->see_my_profiles==3){
                    return true;
                }
            }else{
                if($model->userprivacy->see_my_profiles==3){
                    return true;
                }
            }


        });


        Gate::define('edit-profile', function($user, $model){
            return $user->id === $model->id;
        });


        Gate::define('own-profile', function($user, $model){
            return $user->id === $model->id;
        });



        Gate::define('view-threads', function($user = null, $model){
            if($user != null){
                if($user->id === $model->id){
                    return true;
                }else if($user->is_admin){
                    return true;
                }else if($user->hasBlocked($model) || $user->isBlockedBy($model)){
                    return false;
                }else if($model->userprivacy->see_my_threads==2 && $user->isFriendWith($model)){
                    return true;
                }else if($model->userprivacy->see_my_threads==3){
                    return true;
                }
           }else{
             if($model->userprivacy->see_my_threads==3){
                return true;
            }
           }
        });

        Gate::define('view-favorites', function($user = null, $model){
            if($user != null){
                if($user->id === $model->id){
                    return true;
                }else if($user->is_admin){
                    return true;
                }else if($user->hasBlocked($model) || $user->isBlockedBy($model)){
                    return false;
                }else if($model->userprivacy->see_my_favorites==2 && $user->isFriendWith($model)){
                    return true;
                }else if($model->userprivacy->see_my_favorites==3){
                    return true;
                }
            }else{
                 if($model->userprivacy->see_my_favorites==3){
                    return true;
                }
            }
        });

        Gate::define('view-friends', function($user = null, $model){
            if($user != null){
                if($user->id === $model->id){
                    return true;
                }else if($user->is_admin){
                    return true;
                }else if($user->hasBlocked($model) || $user->isBlockedBy($model)){
                    return false;
                }else if($model->userprivacy->see_my_friends==3){
                    return true;
                }else if($model->userprivacy->see_my_friends==2 && $user->isFriendWith($model)){
                    return true;
                }
            }else{
                if($model->userprivacy->see_my_friends==3){
                    return true;
                }
            }

        });

        Gate::define('send-message', function($user, $model){
            if($user->id === $model->user_id){
                return true;
            }else if($user->is_admin){
                return true;
            }else if($user->hasBlocked($model) || $user->isBlockedBy($model)){
                return false;
            }else if($model->userprivacy->send_me_message==2){
                return true;
            }else if($model->userprivacy->send_me_message==1 && $user->isFriendWith($model)){
                return true;
            }
        });

        Gate::define('update-reply', function($user, $model){
            if($user->id === $model->user_id){
                return true;
            }else if($user->is_admin){
                return true;
            }
            return false;
        });
    }
}
