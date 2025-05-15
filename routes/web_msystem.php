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
    // Route::get('system','WEB\MsystemController@index')->name('system');
    // Route::get('system', function(){
    //     return View::make("msystem.list");
    //  })->name('system');
    Route::get('system','WEB\MsystemController@index')->name('system');
    Route::get('system/get_system_all','WEB\MsystemController@view_data')->name('system.view_data');
    Route::get('system/get_city','WEB\MsystemController@get_city')->name('system.get_city');
    Route::post('system/save_system','WEB\MsystemController@save_data')->name('system.save');
    Route::post('system/edit_system','WEB\MsystemController@edit_data')->name('system.edit');
    Route::post('system/delete_system','WEB\MsystemController@delete_data')->name('system.delete');
    Route::get('delete_system/delete_system/{id}','WEB\MsystemController@delete_system')->name('system.delete-system');
    Route::get('system/get_id/{id}','WEB\MsystemController@get_system_id')->name('system.get_id');
});


