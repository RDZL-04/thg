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
    Route::post('member/save_member', 'API\MemberController@save_member');
    Route::post('member/get_member', 'API\MemberController@get_member');
    Route::post('member/get_member_id', 'API\MemberController@get_member_id');
    Route::post('member/edit_member', 'API\MemberController@edit_member');
    Route::post('member/check_member_device_id', 'API\MemberController@check_member_device_id');
    Route::get('member/{member}/hanging-trans', API\Member\HangingTransController::class);
    Route::post('member/{member}/phone-validator', API\Member\PhoneValidatorController::class);
    Route::post('member/reactive', API\Member\ReactiveController::class);
    Route::post('member/remove', API\Member\RemoveController::class);
});