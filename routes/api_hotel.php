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
    Route::get('hotel/get_hotel_all', 'API\HotelController@get_hotel_all');
    Route::get('hotel/get_hotel_active', 'API\HotelController@get_hotel_active');
    Route::get('hotel/get_hotel_all_with_user_outlet', 'API\HotelController@get_hotel_all_with_user_outlet');
    Route::get('hotel/get_hotel_active_with_user_outlet', 'API\HotelController@get_hotel_active_with_user_outlet');
    Route::get('hotel/get_hotel_id', 'API\HotelController@get_hotel_id');
    Route::get('hotel/get_hotel_user_id', 'API\HotelController@get_hotel_user_id');
    Route::post('hotel/add_hotel', 'API\HotelController@add_hotel');
    Route::post('hotel/add_image_hotel', 'API\HotelController@add_image_hotel');
    // Route::post('hotel/edit_image_hotel', 'API\HotelController@edit_image_hotel');
    Route::post('hotel/delete_image_hotel', 'API\HotelController@delete_image_hotel');
    Route::post('hotel/delete_hotel', 'API\HotelController@delete_hotel');
    Route::post('hotel/edit_hotel', 'API\HotelController@edit_hotel');
    Route::get('hotel/get_hotel_user', 'API\HotelUserController@get_hotel_user');
    Route::post('hotel/add_hotel_user', 'API\HotelUserController@add_hotel_user');
    Route::post('hotel/delete_hotel_user', 'API\HotelUserController@delete_hotel_user');
    Route::get('hotel/get_hotel_images', 'API\HotelController@get_hotel_images');
    Route::get('hotel/get_hotel_all_web_app', 'API\HotelController@get_hotel_all_web_app');
    Route::get('hotel/get_mice_hotel_all_web_app', 'API\HotelController@get_mice_hotel_all_web_app');
    
   });
