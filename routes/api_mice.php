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
    Route::get('mice/get_mice_category_msystem', 'API\MiceController@get_mice_category_msystem');
    Route::post('mice/add_mice_category', 'API\MiceController@add_mice_category');
    Route::post('mice/get_mice_category_detail', 'API\MiceController@get_mice_category_detail');
    Route::post('mice/edit_mice_category', 'API\MiceController@edit_mice_category');
    Route::post('mice/delete_mice_category', 'API\MiceController@delete_mice_category');

    Route::get('mice/get_all_mice_category', 'API\MiceController@get_all_mice_category');
    Route::get('mice/get_hotel_mice_with_hotel_user', 'API\MiceController@get_hotel_mice_with_hotel_user');
    Route::get('mice/get_hotel_mice', 'API\MiceController@get_hotel_mice');
    Route::get('mice/get_all_mice_category_hotel_user_filter', 'API\MiceController@get_all_mice_category_hotel_user_filter');
    Route::get('mice/get_mice_detail', 'API\MiceController@get_mice_detail');
    
});