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
// Route::get('add_hotel_facility','WEB\FacilityController@add_facility')->name('hotel_facility.add');
Route::middleware(['session'])->group( function () {
   
    
    
});
Route::get('payment_fnb','WEB\DiningController@generate_pdf_payment')->name('payment.fnb');
Route::get('payment_reservation','WEB\ReservationController@generate_pdf_payment')->name('payment.reservation');
Route::get('report_fnb','WEB\DiningController@generate_pdf_report_dining')->name('report.dining');

Route::middleware('log.route')->get('payment/finish','WEB\ReservationController@payment_finish')->name('payment.finish');


