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
    Route::get('outlet/get_outlet_all', 'API\OutletController@get_outlet_all');
    Route::get('outlet/get_outlet_active', 'API\OutletController@get_outlet_active');
    Route::get('outlet/get_outlet_all_web', 'API\OutletController@get_outlet_all_web');
    Route::get('outlet/get_hotel_outlet', 'API\OutletController@get_hotel_outlet');
    Route::get('outlet/get_hotel_outlet_with_user', 'API\OutletController@get_hotel_outlet_with_user');
    Route::get('outlet/get_outlet_all_with_user', 'API\OutletController@get_outlet_all_with_user');
    Route::get('outlet/get_outlet_active_with_user', 'API\OutletController@get_outlet_active_with_user');
    Route::get('outlet/get_outlet_detail', 'API\OutletController@get_outlet_detail');
    Route::post('outlet/add_outlet', 'API\OutletController@add_outlet');
    Route::get('outlet/get_outlet_menu', 'API\OutletController@get_outlet_menu');
    Route::get('outlet/get_outlet_images', 'API\OutletController@get_outlet_images');
    Route::post('outlet/delete_outlet', 'API\OutletController@delete_outlet');
    Route::post('outlet/delete_image_outlet', 'API\OutletController@delete_image_outlet');
    Route::post('outlet/edit_outlet', 'API\OutletController@edit_outlet');
    Route::post('outlet/add_image_outlet', 'API\OutletController@add_image_outlet');
    Route::get('outlet/get_image_outlet', 'API\OutletController@get_image_outlet');
    Route::get('outlet/get_image_seq', 'API\OutletController@get_image_seq');
    Route::get('outlet/get_outlet_user', 'API\OutletController@get_outlet_user');
    Route::get('outlet/get_outlet_user_avail', 'API\OutletController@get_outlet_user_avail');
    Route::post('outlet/save_outlet_user', 'API\OutletController@save_outlet_user');
    Route::post('outlet/delete_outlet_user', 'API\OutletController@delete_outlet_user');
    Route::post('outlet/search_outlet_user', 'API\OutletController@search_outlet_user');
    Route::get('outlet/get_outlet_city', 'API\OutletController@get_outlet_city');
    Route::get('outlet/get_waiters_outlet', 'API\OutletController@get_waiters_outlet');
    
    
    Route::middleware('auth:sanctum')->group( function () {
        Route::post('outlet/select_outlet', 'API\OutletController@select_outlet');
    });
});