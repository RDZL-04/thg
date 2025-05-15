<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can hotel API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/
Route::middleware(['language'])->group( function ($locale) {
    
    Route::post('mice/add_hall', 'API\HallController@add_hall');
    Route::get('mice/get_hall_all', 'API\HallController@get_hall_all');
    Route::get('mice/get_hall_detail', 'API\HallController@get_hall_detail');
    Route::post('mice/edit_hall', 'API\HallController@edit_hall');
    Route::post('mice/delete_hall', 'API\HallController@delete_hall');
    Route::post('mice/add_hall_images', 'API\HallController@add_hall_images');
    Route::post('mice/delete_image_hall', 'API\HallController@delete_image_hall');
    
    Route::get('mice/get_hotel_mice_msystem', 'API\HallController@get_hotel_mice_msystem');
    Route::get('mice/get_hotel_hall', 'API\HallController@get_hotel_hall');
    Route::get('mice/get_category_hotel', 'API\HallController@get_category_hotel');
    Route::get('mice/get_hall', 'API\HallController@get_hall');
    Route::get('mice/get_hall_images', 'API\HallController@get_hall_images');
    Route::get('mice/get_all_hall_capacity', 'API\HallController@get_all_hall_capacity');
    Route::get('mice/get_search_hall_capacity', 'API\HallController@get_search_hall_capacity');

});