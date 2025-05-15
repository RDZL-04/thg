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
    Route::get('table/get_all', 'API\TableController@get_table_all');
    Route::get('table/get_by_hotel', 'API\TableController@get_table_by_hotel');
    Route::get('table/get_by_outlet', 'API\TableController@get_table_by_outlet');
    Route::get('table/get_by_id', 'API\TableController@get_table_by_id');
    Route::post('table/add_table', 'API\TableController@add_table');
    Route::post('table/edit_table', 'API\TableController@edit_table');
    Route::post('table/delete_table', 'API\TableController@delete_table');
    Route::get('table/get_table_by_user', 'API\TableController@get_table_by_user');

});