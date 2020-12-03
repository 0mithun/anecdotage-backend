<?php

use Illuminate\Support\Facades\Route;

// Public routes


//users
Route::get('users', 'User\UserController@index');
Route::get('user/{username}', 'User\UserController@findByUsername');


// Route group for authenticated users only
Route::group(['middleware' => ['auth:api']], function(){
    Route::get('me', 'User\MeController@getMe');
    Route::post('logout', 'Auth\LoginController@logout');
    Route::put('settings/profile', 'User\SettingsController@updateProfile');
    Route::put('settings/password', 'User\SettingsController@updatePassword');

    Route::resource('threads', 'Thread\ThreadController')->except(['create','edit']);

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

