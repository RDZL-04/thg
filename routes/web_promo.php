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
    Route::get('promo','WEB\PromoController@index')->name('promo');
    Route::get('promo/get_promo_id/{id}','WEB\PromoController@get_promo_id')->name('promo.get_promo_id');
    Route::get('promo/get_promo_with_hotel_user/{hotel_id}','WEB\PromoController@get_promo_with_hotel_user');
    Route::get('promo/get_promo_with_hotel_user/{hotel_id}/{user_id}','WEB\PromoController@get_promo_with_hotel_user');
    Route::get('promo/get_promo_with_outlet_user/{outlet_id}','WEB\PromoController@get_promo_with_outlet_user');
    Route::get('promo/get_promo_with_outlet_user/{outlet_id}/{user_id}','WEB\PromoController@get_promo_with_outlet_user');
    Route::get('promo/get_data','WEB\PromoController@get_data')->name('promo.get_data');
    Route::get('promo/get_data_with_user','WEB\PromoController@get_data_with_user');
    Route::post('promo/save_promo','WEB\PromoController@save_data')->name('promo.save');
    Route::post('promo/edit_promo','WEB\PromoController@edit_data')->name('promo.edit');
    Route::post('promo/delete_promo','WEB\PromoController@delete_promo')->name('promo.delete');
});


