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
    Route::post('menu/categories/get_categories_all', 'API\MenuController@get_categories_all');
    Route::post('menu/categories/get_categories_all_with_seq_no', 'API\MenuController@get_categories_all_with_seq_no');
    Route::post('menu/categories/get_categories_all_user', 'API\MenuController@get_categories_all_user');
    Route::post('menu/categories/get_menu_category_all_hotel', 'API\MenuController@get_menu_categories_all_hotel');
    Route::post('menu/categories/get_menu_category_all_user_hotel', 'API\MenuController@get_menu_categories_all_user_hotel');
    Route::post('menu/categories/save_menu_categories', 'API\MenuController@save_menu_categories');
    Route::post('menu/categories/delete_menu_categories', 'API\MenuController@delete_menu_categories');
    Route::get('menu/get_menu_detail', 'API\MenuController@get_menu_detail');
    Route::post('menu/save_menu', 'API\MenuController@save_menu');
    Route::post('menu/delete_menu', 'API\MenuController@delete_menu');
    Route::post('menu/get_menu_sidedish', 'API\MenuController@get_menu_sidedish');
    Route::post('menu/get_sidedish', 'API\MenuController@get_sidedish');
    Route::post('menu/get_sidedish_menu_cat', 'API\MenuController@get_sidedish_menu_cat');
    Route::post('menu/add_sidedish', 'API\MenuController@add_sidedish');
    Route::post('menu/delete_sidedish', 'API\MenuController@delete_sidedish');
    Route::get('menu/get_menu', 'API\MenuController@get_menu');
    Route::get('menu/get_menu_outlet', 'API\MenuController@get_menu_outlet');
    Route::get('menu/get_menu_is_sidedish', 'API\MenuController@get_menu_is_sidedish');
    Route::get('menu/get_seq_no', 'API\MenuController@get_seq_no');
});