<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for ALLO
|--------------------------------------------------------------------------
|
| Here is where you can User API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Login Allo via Arka API
Route::post('/allo/create_header_allo', 'API\AlloApiController@create_header_allo')->name('create_header_allo');
Route::post('/allo/create_code_challenge', 'API\AlloApiController@create_code_challenge')->name('create_code_challenge');
Route::post('/allo/decrypt_header', 'API\AlloApiController@decrypt_header')->name('decrypt_header');
Route::post('/allo/allo_login_register','API\AlloApiController@allo_auth_page' )->name('allo_auth_page');
Route::post('/allo/allo_auth_token','API\AlloApiController@allo_auth_token' )->name('allo_auth_page');
Route::post('/allo/refresh_token','API\AlloApiController@refresh_token' )->name('refresh_token');
Route::post('/allo/generate_factor_helper','API\AlloApiController@generate_factor_helper' )->name('generate_factor_helper');
Route::post('/allo/auth_by_token','API\AlloApiController@auth_by_token' )->name('auth_by_token');
Route::post('/allo/get_allo_explorer_url','API\AlloApiController@get_allo_explorer_url' )->name('get_allo_explorer_url');

// ALLO Member Group API 
Route::post('/allo/allo_member_profile','API\AlloApiController@allo_member_profile' )->name('allo_member_profile');
Route::post('/allo/allo_member_change_email','API\AlloApiController@allo_member_change_email' )->name('allo_member_change_email');
Route::post('/allo/allo_member_change_password','API\AlloApiController@allo_member_change_password' )->name('allo_member_change_password');
Route::post('/allo/allo_member_edit_profile','API\AlloApiController@allo_member_edit_profile' )->name('allo_member_edit_profile');

// Allo Point Group API
Route::post('/allo/allo_point_balance','API\AlloApiController@allo_point_balance' )->name('allo_point_balance');
Route::post('/allo/allo_point_history','API\AlloApiController@allo_point_history' )->name('allo_point_history');
Route::post('/allo/allo_point_add','API\AlloApiController@allo_point_add' )->name('allo_point_add');
Route::post('/allo/allo_point_consume','API\AlloApiController@allo_point_consume' )->name('allo_point_consume');

// Allo Coupon Group
Route::post('/allo/coupon_instance_list','API\AlloApiController@coupon_instance_list' )->name('coupon_instance_list');
Route::post('/allo/coupon_query_list','API\AlloApiController@coupon_query_list' )->name('coupon_query_list');
Route::post('/allo/coupon_instance_detail_query','API\AlloApiController@coupon_instance_detail_query' )->name('coupon_instance_detail_query');
Route::middleware([
  \App\Http\Middleware\AlloAccessToken::class,
  \App\Http\Middleware\AlloRefreshToken::class
])->group(function () {
  Route::post('allo/acquirable-coupon-list', API\Gateways\MPC\AcquirableCoupon::class);
  Route::post('allo/all-acquirable-coupon', API\Gateways\MPC\AllAcquirableCoupon::class);
  Route::post('allo/acquire-coupon', API\Gateways\MPC\AcquireCoupon::class);
  Route::post('allo/available-coupon-list', API\Gateways\MPC\AvailableCoupon::class);
  Route::post('allo/all-available-coupon', API\Gateways\MPC\AllAvailableCoupon::class);
  Route::post('allo/all-used-coupon', API\Gateways\MPC\AllUsedCoupon::class);
  Route::post('allo/all-expired-coupon', API\Gateways\MPC\AllExpiredCoupon::class);
  Route::post('allo/detail-coupon', API\Gateways\MPC\DetailCoupon::class);
  Route::post('allo/all-history-point', API\Gateways\MPC\AllHistoryPoint::class);
  Route::post('allo/balance-point', API\Gateways\MPC\BalancePoint::class);
});

// Allo Wallet Group
Route::post('/allo/wallet_registration','API\AlloApiController@wallet_registration' )->name('wallet_registration');
Route::post('/allo/wallet_query_info','API\AlloApiController@wallet_query_info' )->name('wallet_query_info');
Route::post('/allo/wallet_registration_status','API\AlloApiController@wallet_registration_status' )->name('wallet_registration_status');

Route::post('/allo/test','API\AlloApiController@test' )->name('test');

