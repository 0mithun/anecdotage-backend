<?php

use Illuminate\Support\Facades\Route;

Route::get('/all', 'Slide\SlideController@index');
Route::get('/slide/{slide}', 'Slide\SlideController@show');


Route::post('/slide/{slide}/report', 'Slide\SlideController@report');

Route::get('/categories', 'Slide\SlideController@categories');


Route::get('/category/{tag}', 'Slide\SlideController@getByCategory');



//Settings
Route::get('/settings', 'Admin\SlideSettingController@index');

 //User ban
 Route::group(['prefix' => 'admin', 'middleware' => ['auth:api','admin','customcors'], 'namespace' => 'Admin'], function () {
     Route::get('/slides/single/{thread}', 'SlideController@getSingleSlide');
    Route::post('/slide/{thread}', 'SlideController@update');
    Route::post('/slide/{thread}/screenshot', 'SlideController@takeScreenshot');


    //Admin Settings
    Route::put('settings', 'SlideSettingController@update');
    Route::post('settings/logo', 'SlideSettingController@updateLogo');
    Route::post('settings/favicon', 'SlideSettingController@updateFavicon');


    Route::post('/categories', 'SlideController@addCategory');
    Route::put('/categories/{id}', 'SlideController@updateCategory');

});
