<?php

use Illuminate\Support\Facades\Route;

Route::get('/all', 'Slide\SlideController@index');
Route::get('/categories', 'Slide\SlideController@categories');


Route::get('/category/{tag}', 'Slide\SlideController@getByCategory');



//Settings
Route::get('/settings', 'Admin\SlideSettingController@index');

 //User ban
 Route::group(['prefix' => 'admin', 'middleware' => ['auth:api','admin'], 'namespace' => 'Admin'], function () {

    //Admin Settings
    Route::put('settings', 'SlideSettingController@update');
    Route::post('settings/logo', 'SlideSettingController@updateLogo');
    Route::post('settings/favicon', 'SlideSettingController@updateFavicon');

});
