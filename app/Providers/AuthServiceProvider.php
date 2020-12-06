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

        Gate::define('view-profile', function($user, $model){
            if($user->id === $model->id){
                return true;
            }else if($user->is_admin){
                return true;
            }
        });

        Gate::define('edit-profile', function($user, $model){
            return $user->id === $model->id;
        });
    }
}
