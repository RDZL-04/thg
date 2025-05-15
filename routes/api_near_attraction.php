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
    
    Route::get('near/get_near_attraction_hotel', 'API\NearAttractionController@get_near_attraction_hotel');
    Route::get('near/get_nearattraction', 'API\NearAttractionController@get_nearattraction');
    Route::get('near/get_near_radius', 'API\NearAttractionController@get_near_radius'); 
    Route::get('near/get_near_attraction_by_id', 'API\NearAttractionController@get_near_attraction_by_id'); 
    Route::post('near/add_update_near_attraction', 'API\NearAttractionController@add_update_near_attraction'); 
    Route::post('near/delete_near_attraction', 'API\NearAttractionController@delete_near_attraction'); 
    
   });
