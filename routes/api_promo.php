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
    Route::get('promo/get_all', 'API\PromoController@get_promo_all');
    Route::get('promo/get_all_promo_with_hotel', 'API\PromoController@get_all_promo_with_hotel');
    Route::get('promo/get_all_promo_with_outlet', 'API\PromoController@get_all_promo_with_outlet');
    Route::get('promo/get_promo_id', 'API\PromoController@get_promo_id');
    Route::get('promo/get_promo_outlet_with_user', 'API\PromoController@get_promo_outlet_with_user');
    Route::post('promo/add_promo', 'API\PromoController@add_promo');
    Route::post('promo/edit_promo', 'API\PromoController@edit_promo');
    Route::post('promo/delete_promo', 'API\PromoController@delete_promo');
    // Route::post('user/delete_role', 'API\RoleController@delete_role');
    // Route::get('user/get_role', 'API\RoleController@get_role');
});