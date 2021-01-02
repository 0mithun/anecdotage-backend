<?php

namespace App\Providers;

use App\Models\Design;
use App\Observers\DesignObserver;
use Illuminate\Support\ServiceProvider;
use App\Http\Resources\RoomUserResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RoomUserResource::withoutWrapping();
    }
}
