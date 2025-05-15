<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('add_hotel_facility','WEB\FacilityController@add_facility')->name('hotel_facility.add');
Route::middleware(['session'])->group( function () {
   
    Route::get('near/add_attraction','WEB\NearAttractionController@add_attraction')->name('add_attraction');
    Route::get('near/edit_attraction','WEB\NearAttractionController@edit_attraction')->name('edit_attraction');
    Route::post('near/save_attraction','WEB\NearAttractionController@save_attraction')->name('attraction.save');
    Route::get('near/delete_attraction/{id}','WEB\NearAttractionController@delete_attraction')->name('attraction.delete');
    
});


