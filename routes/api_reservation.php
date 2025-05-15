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
    Route::get('reservation/search_hotel/{name}', 'API\ReservationController@get_hotel_by_name');
    Route::post('reservation/hold_reservation', 'API\ReservationController@hold_reservation');
    Route::get('main_page/get_hotels', 'API\ReservationController@get_hotel_main');
    Route::get('reservation/get_reservation', 'API\ReservationController@get_data_reservation');
    Route::get('reservation/discountcode', 'API\ReservationController@get_promo');
    Route::get('reservation/get_room', 'API\ReservationController@get_room');
    Route::get('reservation/get_reservation_member', 'API\ReservationController@get_data_reservation_by_member');
    Route::get('reservation/get_reservation_by_device_id', 'API\ReservationController@get_data_reservation_by_device_id');
    Route::post('reservation/send_mail_reservation', 'API\ReservationController@send_mail_reservation'); 
    Route::post('reservation/update_status_notification', 'API\ReservationController@update_status_notif'); 
    Route::post('reservation/update_os_type_reservation', 'API\ReservationController@update_os_type_reservation'); 
    Route::post('reservation/update_status_payment_reservation', 'API\ReservationController@update_status_payment_reservation'); 
    Route::get('reservation/get_reservation_callback', 'API\ReservationController@get_data_reservation_callback');
    Route::post('reservation/update_status_hold_at', 'API\ReservationController@update_status_hold_at');
    Route::post('reservation/update_tmp_resv', API\UpdateResvTmpController::class);
    Route::get('reservation/get_system_special_request', 'API\ReservationController@get_system_special_request');
    Route::post('reservation/tc_hold_reservation', 'API\ReservationController@tc_hold_reservation');
    
   });
