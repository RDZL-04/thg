<?php

use Illuminate\Support\Facades\Route;

# auth
Route::get('mpc/gen-code', \App\Http\Controllers\API\Gateways\MPC\GenCode::class);
Route::post('mpc/gen-header', \App\Http\Controllers\API\Gateways\MPC\GenHeader::class);
Route::post('mpc/gen-auth', \App\Http\Controllers\API\Gateways\MPC\GenAuth::class);
Route::post('mpc/get-token', \App\Http\Controllers\API\Gateways\MPC\GetToken::class);
Route::post('mpc/refresh-token', \App\Http\Controllers\API\Gateways\MPC\RefreshToken::class);

# redeemable coupon 
Route::post('mpc/available-coupon', \App\Http\Controllers\API\Gateways\MPC\AvailableCoupon::class);
Route::post('mpc/detail-coupon', \App\Http\Controllers\API\Gateways\MPC\DetailCoupon::class); // not fix - unavailable coupon from 3rd party
Route::post('mpc/cal-coupon', \App\Http\Controllers\API\Gateways\MPC\CalPaymentCoupon::class); // not fix - unavailable coupon from 3rd party
Route::post('mpc/redeem-coupon', \App\Http\Controllers\API\Gateways\MPC\RedeemCoupon::class); // not fix - unavailable coupon from 3rd party

# available coupon
Route::post('mpc/acquirable-coupon', \App\Http\Controllers\API\Gateways\MPC\AcquirableCoupon::class);
Route::post('mpc/acquire-coupon', \App\Http\Controllers\API\Gateways\MPC\AcquireCoupon::class);

# point
Route::post('mpc/add-point', \App\Http\Controllers\API\Gateways\MPC\AddPoint::class);
Route::post('mpc/balance-point', \App\Http\Controllers\API\Gateways\MPC\BalancePoint::class);
Route::post('mpc/history-point', \App\Http\Controllers\API\Gateways\MPC\HistoryPoint::class);
Route::post('mpc/consume-point', \App\Http\Controllers\API\Gateways\MPC\ConsumePoint::class);

# dummy TC 
Route::get('tc/dummy/available-rate', \App\Http\Controllers\API\Gateways\TC\AvailableRatePlant::class);