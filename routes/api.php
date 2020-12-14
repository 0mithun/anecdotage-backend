<?php

use Illuminate\Support\Facades\Route;

// Public routes


//users
// Route::get('users', 'User\UserController@index');
// Route::get('user/{user}', 'User\UserController@findByUsername');




//Common Route
 //Channel
 Route::get('channels','Channel\ChannelController@index');

 //Thread
 Route::get('threads', 'Thread\ThreadController@index');

 Route::get('threads/{thread}', 'Thread\ThreadController@show');
 Route::get('trending/threads', 'Thread\TrendingController@index');

//Emojis
Route::resource('emojis', 'Emoji\EmojiController')->only(['index','show']);

//Thread report
Route::post('threads/{thread}/report','Thread\ReportController@report');


  //Tags
  Route::get('tags/{tag}', 'Tag\TagController@show');

// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function(){
    Route::get('me', 'User\MeController@getMe');
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');
    Route::put('settings/avatar', 'User\SettingsController@updateAvatar');

    Route::put('user/{user}/privacy','User\PrivacyController@update');
    Route::put('user/{user}/notification','User\NotificationController@update');


    //Profiles
    Route::get('profile/{user}','User\ProfileController@user');
    Route::get('profile/{user}/subscriptions','User\ProfileController@subscriptions');
    Route::get('profile/{user}/favorites','User\ProfileController@favorites');
    Route::get('profile/{user}/likes','User\ProfileController@likes');
    Route::get('profile/{user}/threads','User\ProfileController@threads');

    // Route::get('/profiles/{user}/comments', 'ProfilesController@myCommentsShow')->name('profile.likes');


    //Threads
    Route::resource('threads', 'Thread\ThreadController')->except(['create','edit', 'index','show']);
    Route::resource('threads.replies', 'Reply\ReplyController')->except(['create','edit']);
    Route::get('threads/{thread}/replies/{reply}/childs','Reply\ReplyController@childs')->name('replies.childs');





    //Emoji
    Route::post('threads/{thread}/emojis', 'Thread\EmojiController@store');
    Route::delete('threads/{thread}/emojis', 'Thread\EmojiController@destroy');

    Route::get('threads/{thread}/emojis/user','Thread\EmojiController@userVoteType');


    //Favorites
    Route::post('threads/{thread}/favorites', 'Thread\FavoriteController@store');
    Route::delete('threads/{thread}/favorites', 'Thread\FavoriteController@destroy');

     //Likes
     Route::post('threads/{thread}/likes', 'Thread\LikeController@store');
     Route::delete('threads/{thread}/likes', 'Thread\LikeController@destroy');


     //Follows user
     Route::post('users/{user}/follow', 'User\FollowController@store');
     Route::delete('users/{user}/follow', 'User\FollowController@destroy');


    //Tags
    Route::resource('tags', 'Tag\TagController')->only(['update','destroy']);
    Route::post('tags/search' ,'Tag\TagController@search');


    //Follows tag
    Route::post('tags/{tag}/follow', 'Tag\FollowController@store');
    Route::delete('tags/{tag}/follow', 'Tag\FollowController@destroy');


    //Channel
    Route::post('/channels/search','Channel\ChannelController@search');


    //Friendship
    Route::post('/user/{user}/friends/sent','Friend\FriendShipController@sentFriendRequestToUser');
    Route::post('/user/{user}/friends/accept','Friend\FriendShipController@acceptFriendRequest');
    Route::post('/user/{user}/friends/denied','Friend\FriendShipController@deniedFriendRequest');
    Route::post('/user/{user}/friends/unfriend','Friend\FriendShipController@unfriendUser');
    Route::post('/user/{user}/friends/block','Friend\FriendShipController@blockFriend');
    Route::post('/user/{user}/friends/unblock','Friend\FriendShipController@unblockUesr');
    Route::post('/user/{user}/friends/is-friend','Friend\FriendShipController@checkIsUserFriendWith');
    Route::post('/user/{user}/friends/check-request-from','Friend\FriendShipController@checkHasSentFriendRequestFrom');
    Route::post('/user/{user}/friends/check-request-to','Friend\FriendShipController@checkHasSentFriendRequestTo');
    Route::post('/user/{user}/friends/check-block','Friend\FriendShipController@checkIsBlock');

    Route::post('/user/{user}/friends/friend-list','Friend\FriendShipController@getAllFriendLists');
    Route::post('/user/{user}/friends/block-list','Friend\FriendShipController@getAllBlokcFriends');
    Route::post('/user/{user}/friends/friend-request-list','Friend\FriendShipController@getAllPendingFriendRequests');


    Route::group(['prefix' => 'chat', 'namespace'=> 'Chat'], function () {

    });



    /**
     * Admin Midleware group
     */

    //User ban
    Route::group(['prefix' => 'admin', 'middleware'=>['admin'],'namespace'=>'Admin'], function () {

        //Ban user
        Route::post('banned/{user}','BanController@store');
        Route::get('banned','BanController@index');
        Route::put('banned/{user}','BanController@update');
        Route::delete('banned/{user}','BanController@destroy');

        //Admin Settings
        Route::resource('settings', 'SettingController')->only(['index','show','update']);

        //Batch Tools
        Route::group(['prefix' => 'batch-tool','namespace'=>'BatchTool'], function () {
            //Delete Threads
            Route::post('threads/delete-threads-title','DeleteThreadsController@title');
            Route::post('threads/delete-threads-body','DeleteThreadsController@body');
            Route::post('threads/delete-threads-tag','DeleteThreadsController@tag');

            //Set age restriction 13
            Route::post('threads/set-age-thirteen-threads-title','SetAgeThirteenController@title');
            Route::post('threads/set-age-thirteen-threads-body','SetAgeThirteenController@body');
            Route::post('threads/set-age-thirteen-threads-tag','SetAgeThirteenController@tag');

            //Set age restriction 18
            Route::post('threads/set-age-eighteen-threads-title','SetAgeThirteenController@title');
            Route::post('threads/set-age-eighteen-threads-body','SetAgeThirteenController@body');
            Route::post('threads/set-age-eighteen-threads-tag','SetAgeThirteenController@tag');

            //thread search & replace
            Route::post('threads/threads-replace-title','ThreadSearchReplaceController@title');
            Route::post('threads/threads-replace-body','ThreadSearchReplaceController@body');


             //thread add tag
             Route::post('threads/threads-add-tag-title','ThreadAddTagController@title');
             Route::post('threads/threads-add-tag-body','ThreadAddTagController@body');
             Route::post('threads/threads-add-tag-with-tag','ThreadAddTagController@tag');

             //Modify Tag
             Route::post('tag/rename-tag','ModifyTagController@rename');
             Route::post('tag/delete-tag','ModifyTagController@delete');

             //Add Emoji
             Route::post('threads/add-emoji','AddEmojiController@add');

             //Set thread to famous
             Route::post('threads/set-famous-threads-title','SetFamousController@title');
             Route::post('threads/set-famous-threads-body','SetFamousController@body');
             Route::post('threads/set-famous-threads-tag','SetFamousController@tag');

             //Replace Source
             Route::post('threads/replace-source','ReplaceSourceController@replace');

             //Assign to users
             Route::post('threads/assign-to-user-threads-title','AssignToUserController@title');
             Route::post('threads/assign-to-user-threads-body','AssignToUserController@body');
             Route::post('threads/assign-to-user-threads-tag','AssignToUserController@tag');

        });

    });

});

// Routes for guests only
Route::group(['middleware' => ['guest:api']], function(){
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});

