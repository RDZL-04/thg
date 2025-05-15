<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can hotel API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/
Route::middleware(['language'])->group( function ($locale) {
    Route::post('user/add_role', 'API\RoleController@add_role');
    Route::post('user/edit_role', 'API\RoleController@edit_role');
    Route::post('user/delete_role', 'API\RoleController@delete_role');
    Route::get('user/get_role', 'API\RoleController@get_role');
});