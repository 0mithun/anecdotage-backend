<?php

use Illuminate\Support\Facades\Route;

Route::get('/all', 'Slide\SlideController@index');
Route::get('/categories', 'Slide\SlideController@categories');
