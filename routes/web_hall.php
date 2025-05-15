<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hall Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/ 
Route::middleware(['session'])->group( function () {

    Route::get('mice/hall','WEB\HallController@hall_index')->name('hall');
    Route::get('mice/hall_new','WEB\HallController@hall_new')->name('hall.new');
    Route::post('mice/hall_add','WEB\HallController@hall_add')->name('hall.add');
    Route::get('mice/get_edit_hall/{id}','WEB\HallController@get_edit_hall')->name('hall.get_edit');
    Route::post('mice/edit_hall','WEB\HallController@edit_hall')->name('hall.edit');
    Route::get('mice/delete_hall/{id}','WEB\HallController@delete_hall');
    Route::get('mice/get_hotel_mice_msystem/{hotel_id}','WEB\HallController@get_hotel_mice_msystem');
    Route::get('mice/images_hall','WEB\HallController@images_hall')->name('images_hall');
    Route::post('mice/images_hall_store','WEB\HallController@save_image')->name('hall_images.store');
    Route::get('mice/delete_hall_images/{id}','WEB\HallController@delete_hall_images');
    Route::get('mice/get_edit_hall_images','WEB\HallController@get_edit_images')->name('hall_images.get_edit');
    Route::get('mice/get_hotel_hall/{id}','WEB\HallController@get_hotel_hall');
    
});


