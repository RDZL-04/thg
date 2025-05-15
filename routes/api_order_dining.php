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
    Route::post('dining/order_dining', 'API\DiningController@order_dining');
    Route::get('dining/scan_barcode', 'API\TableController@scan_barcode');
    Route::get('dining/get_order_fb', 'API\DiningController@get_order_dining');
    Route::get('dining/get_order_callback', 'API\DiningController@get_order_dining_callback');
    
    // Route::get('dining/get_order_on_progress', 'API\DiningController@get_order_progress');
    // Route::get('dining/get_order_done', 'API\DiningController@get_order_done');
    // Route::get('dining/get_order_failed', 'API\DiningController@get_order_failed');
    Route::post('dining/approve_order', 'API\DiningController@approve_order');
    Route::post('dining/update_payment_dining', 'API\DiningController@update_payment_dining');
    Route::get('dining/get_order_dining_non_member', 'API\DiningController@get_order_dining_non_member');
    Route::get('dining/get_order_dining_by_device_id', 'API\DiningController@get_order_dining_by_device_id');
    // Route::get('dining/download_payment', 'API\DiningController@generate_pdf_payment');
    Route::get('dining/manage/get_order_dining', 'API\DiningController@get_order');
    Route::post('dining/manage/add_approver', 'API\DiningController@add_approver');
    Route::post('dining/order_dining_failed', 'API\DiningController@replace_order_dining_failed');
    Route::post('dining/update_status_dining', 'API\DiningController@update_status_dining');
    Route::post('dining/update_os_type_dining', 'API\DiningController@update_os_type_dining');
    Route::post('dining/replace_order_dining', 'API\DiningController@replace_order_dining');
    Route::get('dining/cancel_order_otomatis', 'API\DiningController@cancel_order_otomatis');
    
    // Route::post('reservation/hold_reservation', 'API\ReservationController@hold_reservation');
    // Route::get('main_page/get_hotels', 'API\ReservationController@get_hotel_main');
    
    
   });
