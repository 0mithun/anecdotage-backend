<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{
    IEmoji,
    IReply,
    IUser,
    IThread
};
use App\Repositories\Eloquent\{
    EmojiRepository,
    ReplyRepository,
    UserRepository,
    ThreadRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(IUser::class, UserRepository::class);
        $this->app->bind(IThread::class, ThreadRepository::class);
        $this->app->bind(IReply::class, ReplyRepository::class);
        $this->app->bind(IEmoji::class, EmojiRepository::class);
    }
}
