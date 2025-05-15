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
    // Route::get('role', function(){
    //     return View::make("role.list");
    //  })->name('role');
    Route::get('permission','WEB\PermissionController@index')->name('permission');
    Route::get('permission/get','WEB\PermissionController@get')->name('permission.get');
    Route::post('permission/set','WEB\PermissionController@set')->name('permission.set');
    //Route::post('role/save_role','WEB\RoleController@save_data')->name('role.save');
    //Route::post('role/edit_role','WEB\RoleController@edit_data')->name('role.edit');
    //Route::post('role/delete_role','WEB\RoleController@delete_role')->name('role.delete');
});


