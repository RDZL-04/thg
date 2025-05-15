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
    Route::get('msytem/get_system', 'API\MsystemController@get_system');
    Route::post('msytem/add_system', 'API\MsystemController@add_system');
    Route::post('msytem/edit_system', 'API\MsystemController@edit_system');
    Route::post('msytem/delete_system', 'API\MsystemController@delete_system');
    Route::get('msytem/get_system_type_cd', 'API\MsystemController@get_system_type_cd');
    Route::get('msytem/get_by_type_cd', 'API\MsystemController@getByTypeCD');
    Route::get('msystem/get_list_country', 'API\MsystemController@get_system_country');
    Route::get('msystem/get_list_city', 'API\MsystemController@get_system_city');
    Route::get('msystem/get_payment_source', 'API\MsystemController@get_payment_source');
    Route::get('msystem/get_system_id', 'API\MsystemController@get_system_id');
    Route::get('sign', 'API\MsystemController@generate_sign');
   });
