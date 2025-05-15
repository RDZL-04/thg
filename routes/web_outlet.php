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
    // Outlet
    Route::get('master/fnboutlet','WEB\OutletController@index')->name('outlet');
    Route::get('master/fnboutlet/get_outlet_all','WEB\OutletController@get_outlet_all');
    Route::get('master/fnboutlet/get_outlet_all_with_user_outlet','WEB\OutletController@get_outlet_all_with_user_outlet');
    Route::get('master/fnboutlet/get_outlet_detail/{outlet_id}','WEB\OutletController@get_outlet_detail');
    Route::get('master/fnboutlet/get_hotel_outlet/{hotel_id}','WEB\OutletController@get_hotel_outlet');
    Route::get('master/fnboutlet/get_hotel_outlet_with_user/{hotel_id}/{user_id}','WEB\OutletController@get_hotel_outlet_with_user_id');
    Route::get('master/fnboutlet/add_outlet','WEB\OutletController@add_outlet')->name('outlet.add');
    Route::get('master/fnboutlet/get_hotel_id/{id}','WEB\OutletController@get_hotel_id');
    Route::post('master/fnboutlet/save_outlet','WEB\OutletController@save_outlet')->name('outlet.store');
    Route::get('master/fnboutlet/get_edit_outlet/{id}','WEB\OutletController@get_edit_outlet')->name('outlet.get_edit');
    Route::post('master/fnboutlet/edit_outlet','WEB\OutletController@edit_outlet')->name('outlet.edit');
    Route::get('master/fnboutlet/delete_outlet/{id}','WEB\OutletController@delete_outlet')->name('outlet.delete');
    // Image Outlet
    Route::get('master/fnboutlet/images/images_outlet','WEB\OutletController@images_outlet')->name('outlet_images');
    Route::post('master/fnboutlet/images/images_outlet_store','WEB\OutletController@save_image')->name('outlet_images.store');
    Route::get('master/fnboutlet/images/delete_outlet_images/{id}','WEB\OutletController@delete_images')->name('outlet_images.delete');
    Route::get('master/fnboutlet/images/get_edit_outlet_images','WEB\OutletController@get_edit_images')->name('outlet_images.get_edit');
    Route::post('master/fnboutlet/images/edit_outlet_images','WEB\OutletController@edit_images')->name('outlet_images.edit');
    // Menu Outlet
    Route::get('master/fnboutlet/menu/get_edit_outlet_menu_add','WEB\MenuController@get_menu_add')->name('outlet_menu.add');
    Route::get('master/fnboutlet/get_edit_outlet/edit_menu/{outlet_id}/{id}','WEB\MenuController@get_menu_edit')->name('outlet_menu.get_edit');
    Route::get('master/fnboutlet/get_edit_outlet/delete_menu/{id}','WEB\MenuController@delete_menu');
    Route::post('master/fnboutlet/get_edit_outlet/edit_menu','WEB\MenuController@get_edit')->name('outlet_menu.edit');
    Route::post('master/fnboutlet/menu/add_sidedish','WEB\MenuController@add_sidedish');
    Route::get('master/fnboutlet/menu/delete_sidedish/{outlet_id}/{menu_id}/{sidedish_id}','WEB\MenuController@delete_sidedish');
    Route::get('master/fnboutlet/get_edit_outlet/edit_menu/{outlet_id}/get_menu_from_category/{menu_id}/{id}','WEB\MenuController@get_menu_from_category');
    // User Outlet
    Route::get('master/fnboutlet/user/add_user','WEB\MenuController@add_user')->name('outlet_user.add');
    Route::post('master/fnboutlet/user/save_user','WEB\MenuController@save_user')->name('outlet_user.store');
    Route::get('master/fnboutlet/get_edit_outlet/delete_outlet_user/{id}','WEB\MenuController@delete_outlet_user');
    // Menu Categories
    Route::get('master/fnboutlet/menu_category','WEB\MenuController@view_category')->name('outlet.menu-category');
    Route::get('master/fnboutlet/get_outlet_all','WEB\MenuController@get_outlet_all');
    Route::get('master/fnboutlet/get_menu_category_all','WEB\MenuController@get_menu_category_all');
    Route::get('master/fnboutlet/get_menu_category_all_user','WEB\MenuController@get_menu_category_all_user');
    Route::get('master/fnboutlet/get_menu_category_all_hotel','WEB\MenuController@get_menu_category_all_hotel');
    Route::get('master/fnboutlet/get_menu_category_all_hotel/{hotel_id}','WEB\MenuController@get_menu_category_all_hotel');
    Route::get('master/fnboutlet/get_menu_category_all_user_hotel/{user_id}','WEB\MenuController@get_menu_category_all_user_hotel');
    Route::get('master/fnboutlet/get_menu_category_all_user_hotel/{hotel_id}/{user_id}','WEB\MenuController@get_menu_category_all_user_hotel');
    Route::post('master/fnboutlet/menu/category/save_menu_category','WEB\MenuController@save_menu_categories');
    Route::get('master/fnboutlet/menu/category/delete_menu_category/{id}','WEB\MenuController@delete_menu_category');
    Route::get('master/fnboutlet/menu/category/get_seq_no_menu_category/{outlet_id}','WEB\MenuController@get_seq_no_menu_category');

});


