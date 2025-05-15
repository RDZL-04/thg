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
    
    Route::get('table','WEB\TableController@get_table')->name('table');
    Route::get('table/get_data','WEB\TableController@get_data')->name('table.get_data');
    Route::get('table/get_data_by_hotel/{id_hotel}','WEB\TableController@get_data_by_hotel')->name('table.get_data_by_hotel');
    Route::get('table/get_data_by_hotel_with_user/{id_hotel}/{id_user}','WEB\TableController@get_data_by_hotel_with_user');
    Route::get('table/get_data_by_outlet/{id_outlet}','WEB\TableController@get_data_by_outlet')->name('table.get_data_by_outlet');
    Route::get('table/get_data_by_id/{id}','WEB\TableController@get_data_by_id')->name('table.get_data_by_id');
    Route::post('table/save_table','WEB\TableController@save_data')->name('table.save');
    Route::post('table/edit_table','WEB\TableController@edit_data')->name('table.edit');
    Route::get('table/delete_table/{id}','WEB\TableController@delete_table')->name('table.delete');
    Route::get('table/barcode/{data}', 'WEB\TableController@barcode')->name('table.barcode');
    Route::get('table/qrcode', function () {
        return QrCode::size(250)
            ->backgroundColor(255, 255, 204)
            ->generate('MyNotePaper');})->name('qrcode');
});


