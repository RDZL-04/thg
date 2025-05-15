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

    Route::get('mice/mice_category','WEB\MiceController@mice_category_index')->name('mice_category');
    Route::get('mice/new_mice_category','WEB\MiceController@new_mice_category')->name('mice_category.new');
    Route::get('mice/get_edit_mice_category/{id}','WEB\MiceController@get_edit_mice_category')->name('mice_category.edit');
    Route::post('mice/add_mice_category','WEB\MiceController@add_mice_category')->name('mice_category.store');
    Route::post('mice/edit_mice_category','WEB\MiceController@edit_mice_category')->name('mice_category.edit');
    Route::get('mice/delete_mice_category/{id}','WEB\MiceController@delete_mice_category');
    Route::get('mice/get_all_mice_category_with_hotel/{hotel_id}','WEB\MiceController@get_all_mice_category_with_hotel');
    Route::get('mice/get_all_mice_category_with_hotel/{hotel_id}/{user_id}','WEB\MiceController@get_all_mice_category_with_hotel');
    Route::get('mice/get_all_mice_category','WEB\MiceController@get_all_mice_category');
    
    
});


