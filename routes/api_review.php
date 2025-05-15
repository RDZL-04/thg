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
    Route::get('hotel/get_review_summary', 'API\ReviewController@get_review_hotel');
    Route::get('hotel/get_review_hotel', 'API\ReviewController@get_review_hotel_all');
    Route::get('hotel/get_review_detail', 'API\ReviewController@get_review_hotel_detail');
    Route::get('hotel/get_review_reservation', 'API\ReviewController@get_review_reservation');
    Route::Post('hotel/add_review_summary', 'API\ReviewController@add_review_hotel');
    Route::Post('hotel/edit_review_summary', 'API\ReviewController@edit_review_hotel');
    Route::post('hotel/get_review_user', 'API\ReviewController@get_review_user');
   });
