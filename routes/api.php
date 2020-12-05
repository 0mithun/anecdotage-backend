<?php

use Illuminate\Support\Facades\Route;

// Public routes


//users
// Route::get('users', 'User\UserController@index');
// Route::get('user/{user}', 'User\UserController@findByUsername');


// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function(){
    Route::get('me', 'User\MeController@getMe');
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');
    Route::put('settings/avatar', 'User\SettingsController@updateAvatar');


    //Profiles

    Route::get('profile/{user}','User\ProfileController@user');
    Route::get('profile/{user}/subscriptions','User\ProfileController@subscriptions');
    Route::get('profile/{user}/favorites','User\ProfileController@favorites');
    Route::get('profile/{user}/likes','User\ProfileController@likes');
    Route::get('profile/{user}/threads','User\ProfileController@threads');

    // Route::get('/profiles/{user}/comments', 'ProfilesController@myCommentsShow')->name('profile.likes');
    // Route::get('/profiles/{user}/threads', 'ProfilesController@myThreadsShow')->name('profile.threads');


    //Threads
    Route::resource('threads', 'Thread\ThreadController')->except(['create','edit']);
    Route::resource('threads.replies', 'Reply\ReplyController')->except(['create','edit']);
    Route::get('threads/{thread}/replies/{reply}/childs','Reply\ReplyController@childs')->name('replies.childs');


    //Emojis
    Route::resource('emojis', 'Emoji\EmojiController')->only(['index','show']);
    Route::resource('threads.emojis', 'Thread\EmojiController')->only(['store','destroy']);
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
    Route::resource('tags', 'Tag\TagController')->only(['show','update','destroy']);
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

