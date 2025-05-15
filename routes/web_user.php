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
    // Route::get('user', function(){
    //     return View::make("user.list");
    //  })->name('user');
    Route::get('user','WEB\UserController@indexUser')->name('user');
    Route::get('user/get_data','WEB\UserController@get_data')->name('user.get_data');
    Route::post('user/save_user','WEB\UserController@save_data')->name('user.save');
    Route::post('user/edit_user','WEB\UserController@edit_data')->name('user.edit');
    Route::post('user/delete_user','WEB\UserController@delete_user')->name('user.delete');
    Route::get('user/get_user_id/{id_user}','WEB\UserController@get_user_id')->name('user.get_user_id');
    Route::get('user/detail.profile','WEB\UserController@get_user')->name('user.detail.profile');
    Route::post('user/edit_profile','WEB\UserController@edit_profile')->name('user.edit.profile');
    Route::post('user/change_password','WEB\UserController@change_password')->name('user.change_password');
    Route::post('user/edit_images','WEB\UserController@edit_profile_image')->name('user.save.images');
    Route::get('user/get_province/{country_id}','WEB\UserController@get_province');
});


