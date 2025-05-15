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
   
    Route::get('facility','WEB\FacilityController@facility')->name('facility');
    Route::post('save_facility','WEB\FacilityController@save_facility')->name('facility.add');
    Route::get('delete_facility/{id}','WEB\FacilityController@delete_facility')->name('facility.delete');
    Route::get('add_hotel_facility','WEB\FacilityController@add_facility')->name('hotel_facility.add');
    Route::get('add_hotel_facility_all','WEB\FacilityController@add_facility_all')->name('hotel_facility_all.add');
    Route::get('delete_hotel_facility_all','WEB\FacilityController@delete_facility_all')->name('hotel_facility_all.delete');
    Route::get('delete_hotel_facility','WEB\FacilityController@delete_hotel_facility')->name('hotel_facility.delete');
    Route::get('edit_hotel_facility','WEB\FacilityController@edit_hotel_facility')->name('hotel_facility.edit');
    
});


