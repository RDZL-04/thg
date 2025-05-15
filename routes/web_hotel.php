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
Route::middleware(['session'])->group( function () {
    // Route::get('hotel', function(){
    //     return View::make("hotel.list");
    //  })->name('hotel');
    Route::get('hotel','WEB\HotelController@list_hotel')->name('hotel');
    Route::get('hotel/get_hotel_all','WEB\HotelController@get_data')->name('hotel.get_data');
    Route::get('add_hotel','WEB\HotelController@add_hotel')->name('hotel.add');
    Route::get('get_edit_hotel/{id}','WEB\HotelController@get_edit_hotel')->name('hotel.get_edit');
    // Route::get('delete_hotel/{id}','WEB\HotelController@delete_hotel')->name('hotel.delete');
    Route::get('facility','WEB\HotelController@facility')->name('facility');
    Route::get('images_hotel','WEB\HotelController@images_hotel')->name('images_hotel');
    Route::post('images_hotel_store','WEB\HotelController@save_image')->name('hotel_images.store');
    Route::get('delete_hotel_images/{id}','WEB\HotelController@delete_images')->name('hotel_images.delete');
    Route::post('hotel/delete_hotel','WEB\HotelController@delete_hotel')->name('hotel.delete');
    Route::get('get_edit_hotel_images','WEB\HotelController@get_edit_images')->name('hotel_images.get_edit');
    Route::post('edit_hotel_images','WEB\HotelController@edit_images')->name('hotel_images.edit');
    Route::post('save_facility','WEB\HotelController@save_facility')->name('facility.add');
    Route::post('save_hotel','WEB\HotelController@save_hotel')->name('hotel.store');
    Route::post('edit_hotel','WEB\HotelController@edit_hotel')->name('hotel.edit');
    Route::get('add_user_hotel','WEB\HotelController@add_user_hotel')->name('add_user_hotel');
    Route::post('save_user_hotel','WEB\HotelController@save_user_hotel')->name('user_hotel.save');
    Route::get('hotel/delete_hotel_user/{id}/{hotel_id}','WEB\HotelController@delete_hotel_user')->name('hotel.delete_hotel_user');
    
    
});


