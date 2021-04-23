<?php

use Illuminate\Support\Facades\Route;

// Public routes


//users
// Route::get('users', 'User\UserController@index');
// Route::get('user/{user}', 'User\UserController@findByUsername');




//Common Route
//Channel
Route::get('channels', 'Channel\ChannelController@index');

//Thread
Route::get('threads', 'Thread\ThreadController@index');

Route::get('threads/filter/rated', 'Thread\FilterController@rated');
Route::get('threads/filter/trending', 'Thread\FilterController@trending');
Route::get('threads/filter/viewed', 'Thread\FilterController@viewed');
Route::get('threads/filter/recent', 'Thread\FilterController@recent');
Route::get('threads/filter/closest', 'Thread\FilterController@closest');
Route::get('threads/filter/video', 'Thread\FilterController@video');

Route::get('threads/{thread}', 'Thread\ThreadController@show');
Route::get('trending/threads', 'Thread\TrendingController@index');

//Emojis
Route::resource('emojis', 'Emoji\EmojiController')->only(['index', 'show']);

//Thread report
Route::post('threads/{thread}/report', 'Thread\ReportController@report');


//Tags
Route::get('tags/{tag}', 'Tag\TagController@show');

//Channel
Route::get('channel/search', 'Channel\ChannelController@search');

//Tags
Route::get('tag/search', 'Tag\TagController@search');


//Replies
Route::get('threads/{thread}/replies/', 'Reply\ReplyController@index');
Route::get('threads/{thread}/replies/{reply}/childs', 'Reply\ReplyController@childs')->name('replies.childs');



//Profiles
Route::get('profile/{user}', 'User\ProfileController@user');
Route::get('profile/{user}/subscriptions', 'User\ProfileController@subscriptions');
Route::get('profile/{user}/favorites', 'User\ProfileController@favorites');
Route::get('profile/{user}/likes', 'User\ProfileController@likes');
Route::get('profile/{user}/threads', 'User\ProfileController@threads');


Route::get('/user/{user}/friends/friend-list', 'Friend\FriendShipController@getAllFriendLists');
Route::get('/user/{user}/friends/block-list', 'Friend\FriendShipController@getAllBlokcFriends');
Route::get('/user/{user}/friends/friend-request-list', 'Friend\FriendShipController@getAllPendingFriendRequests');
Route::get('/user/{user}/friends/followings', 'User\FollowController@followings');
Route::get('/user/{user}/friends/followers', 'User\FollowController@followers');


//Settings
Route::get('/settings', 'Admin\SettingController@index');

//Mapgs
Route::get('maps', 'Maps\ThreadsCotnroller@getAllThread');

//Search
Route::get('search', 'Search\ThreadController@index');

//contact
Route::post('contact', 'Frontend\ContactController@contact');

// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('me', 'User\MeController@getMe');
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');
    Route::post('settings/avatar', 'User\SettingsController@updateAvatar');
    Route::put('settings/about', 'User\SettingsController@updateAbout');
    Route::put('settings/location', 'User\SettingsController@updateLoction');

    Route::get('user/{user}/privacy', 'User\PrivacyController@show');
    Route::put('user/{user}/privacy', 'User\PrivacyController@update');

    Route::get('user/{user}/notification', 'User\NotificationController@show');
    Route::put('user/{user}/notification', 'User\NotificationController@update');

    Route::get('user/{user}/notifications', 'User\NotificationController@notifications');
    Route::put('user/{user}/markAsRead/{id}', 'User\NotificationController@markAsRead');




    // Route::get('/profiles/{user}/comments', 'ProfilesController@myCommentsShow')->name('profile.likes');


    //Threads
    Route::resource('threads', 'Thread\ThreadController')->except(['create', 'edit', 'index', 'show']);
    Route::post('threads/{thread}/thumbnail', 'Thread\ThreadController@uploadThreadImages');
    Route::put('threads/{thread}/imageDescription', 'Thread\ThreadController@imageDescription');

    Route::post('threads/{thread}/duplicateImage', 'Thread\ThreadController@duplicateImage');

    Route::put('threads/{thread}/skipThumbnailEdit', 'Thread\ThreadController@skipThumbnailEdit');
    Route::post('threads/{thread}/share', 'Thread\ThreadController@share');

    //Replies
    Route::resource('threads.replies', 'Reply\ReplyController')->except(['create', 'edit', 'index']);








    //Emoji
    Route::post('threads/{thread}/emojis', 'Thread\EmojiController@store');
    Route::delete('threads/{thread}/emojis', 'Thread\EmojiController@destroy');

    Route::get('threads/{thread}/emojis/user', 'Thread\EmojiController@userVoteType');


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
    Route::post('tags/{tag}', 'Tag\TagController@update');
    // Route::delete('tags/{tag}', 'Tag\TagController@destroy');



    //Follows tag
    Route::post('tags/{tag}/follow', 'Tag\FollowController@store');
    Route::delete('tags/{tag}/follow', 'Tag\FollowController@destroy');





    //Friendship
    Route::post('/user/{user}/friends/sent', 'Friend\FriendShipController@sentFriendRequestToUser');
    Route::post('/user/{user}/friends/accept', 'Friend\FriendShipController@acceptFriendRequest');
    Route::post('/user/{user}/friends/cancel', 'Friend\FriendShipController@cancelFriendRequest');
    Route::post('/user/{user}/friends/unfriend', 'Friend\FriendShipController@unfriendUser');
    Route::post('/user/{user}/friends/block', 'Friend\FriendShipController@blockFriend');
    Route::post('/user/{user}/friends/unblock', 'Friend\FriendShipController@unblockUesr');
    Route::post('/user/{user}/friends/is-friend', 'Friend\FriendShipController@checkIsUserFriendWith');
    Route::post('/user/{user}/friends/check-request-from', 'Friend\FriendShipController@checkHasSentFriendRequestFrom');
    Route::post('/user/{user}/friends/check-request-to', 'Friend\FriendShipController@checkHasSentFriendRequestTo');
    Route::post('/user/{user}/friends/check-block', 'Friend\FriendShipController@checkIsBlock');




    Route::group(['prefix' => 'chat', 'namespace' => 'Chat'], function () {
        // Route::get('rooms','RoomController@index');
        // Route::get('rooms/{room}','RoomController@show');
        Route::get('chat-users-list', 'ChatController@getAllChatLists');
        Route::get('user/{user}/messages', 'ChatController@getFriendMessage');
        Route::get('user/{user}/last-seen', 'ChatController@lastSeen');
        Route::get('user/notifications', 'ChatController@notifications');
        Route::put('user/notifications/{id}', 'ChatController@markAsRead');
        Route::post('user/message-seen', 'ChatController@messageSeen');
        Route::post('user/{user}/messages', 'ChatController@sendMessage');
    });



    /**
     * Admin Midleware group
     */

    //User ban
    Route::group(['prefix' => 'admin', 'middleware' => ['admin'], 'namespace' => 'Admin'], function () {

        Route::put('threads/{thread}', 'ThreadController@update');
        Route::get('threads/sort-by-title-length', 'ThreadController@sortByTitleLength');

        Route::resource('reports', 'ReportController')->only(['index', 'destroy']);

        //Admin Settings
        Route::put('settings', 'SettingController@update');
        Route::post('settings/logo', 'SettingController@updateLogo');
        Route::post('settings/favicon', 'SettingController@updateFavicon');

        //Batch Tools
        Route::group(['prefix' => 'batch-tool', 'namespace' => 'BatchTool'], function () {
            //Delete Threads
            Route::post('threads/delete-threads-title', 'DeleteThreadsController@title');
            Route::post('threads/delete-threads-body', 'DeleteThreadsController@body');
            Route::post('threads/delete-threads-tag', 'DeleteThreadsController@tag');

            //Set age restriction 13
            Route::post('threads/set-age-thirteen-threads-title', 'SetAgeThirteenController@title');
            Route::post('threads/set-age-thirteen-threads-body', 'SetAgeThirteenController@body');
            Route::post('threads/set-age-thirteen-threads-tag', 'SetAgeThirteenController@tag');

            //Set age restriction 18
            Route::post('threads/set-age-eighteen-threads-title', 'SetAgeEighteenController@title');
            Route::post('threads/set-age-eighteen-threads-body', 'SetAgeEighteenController@body');
            Route::post('threads/set-age-eighteen-threads-tag', 'SetAgeEighteenController@tag');

            //thread search & replace
            Route::post('threads/threads-replace-title', 'ThreadSearchReplaceController@title');
            Route::post('threads/threads-replace-body', 'ThreadSearchReplaceController@body');


            //thread add tag
            Route::post('threads/threads-add-tag-title', 'ThreadAddTagController@title');
            Route::post('threads/threads-add-tag-body', 'ThreadAddTagController@body');
            Route::post('threads/threads-add-tag-with-tag', 'ThreadAddTagController@tag');

            //Modify Tag
            Route::post('tag/rename-tag', 'ModifyTagController@rename');
            Route::post('tag/delete-tag', 'ModifyTagController@delete');

            //Add Emoji
            Route::post('threads/add-emoji', 'AddEmojiController@add');

            //Set thread to famous
            Route::post('threads/set-famous-threads-title', 'SetFamousController@title');
            Route::post('threads/set-famous-threads-body', 'SetFamousController@body');
            Route::post('threads/set-famous-threads-tag', 'SetFamousController@tag');

            //Replace Source
            Route::post('threads/replace-source', 'ReplaceSourceController@replace');

            //Assign to users
            Route::post('threads/assign-to-user-threads-title', 'AssignToUserController@title');
            Route::post('threads/assign-to-user-threads-body', 'AssignToUserController@body');
            Route::post('threads/assign-to-user-threads-tag', 'AssignToUserController@tag');
        });

        Route::group(['prefix' => 'manage-users'], function () {
            //Ban user
            // Route::get('banned','BanController@index');

            Route::post('threads/ban-users-title', 'BanController@title');
            Route::post('threads/ban-users-body', 'BanController@body');
            Route::post('threads/ban-users-tag', 'BanController@tag');
            Route::post('threads/unban-all-users', 'BanController@unbanAllUser');

            Route::post('threads/ban-single-user', 'BanController@banSingleUser');
            Route::post('threads/unban-single-user', 'BanController@unBanSingleUser');
        });
    });
});

// Routes for guests only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
});


Route::group([['middleware' => 'throttle:20,5']], function () {
    Route::get('/login/{service}', 'Auth\SocialLoginController@redirect');
    Route::get('/login/{service}/callback', 'Auth\SocialLoginController@callback');
});
