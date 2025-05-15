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
    
    Route::get('hotel/get_facility', 'API\FacilityController@get_facility');
    Route::post('hotel/add_facility', 'API\FacilityController@add_facility');
    Route::post('hotel/add_hotel_facility_all', 'API\FacilityController@add_hotel_facility_all');
    Route::post('hotel/delete_hotel_facility_all', 'API\FacilityController@delete_hotel_facility_all');
    Route::post('hotel/delete_facility', 'API\FacilityController@delete_facility');
    Route::post('hotel/add_hotel_facility', 'API\FacilityController@add_hotel_facility');
    Route::post('hotel/delete_hotel_facility', 'API\FacilityController@delete_hotel_facility');

    
    
   });
