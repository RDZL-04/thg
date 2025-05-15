<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can User API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['language'])->group( function ($locale) {
   
    Route::post('login/check_password', 'API\UserController@check_password');
    Route::post('login/check_phone', 'API\UserController@check_phone');
    Route::post('registration/user_registration', 'API\UserController@save_regist');
    Route::post('registration/user_verification', 'API\UserController@user_verification');
    Route::post('registration/send_otp', 'API\UserController@resend_otp');
    Route::post('login/send_otp_login', 'API\UserController@resend_otp_login');
    Route::post('registration/registration_sso', 'API\UserController@save_user_sso');
    Route::get('user/get_user', 'API\UserController@get_user');
    Route::post('lang', 'API\UserController@switchlang');
});

Route::post('login/check_otp', 'API\UserController@check_otp');
Route::post('login', 'API\UserController@login');
Route::post('outlet/login', 'API\UserController@login_outlet');

Route::post('outlet/forgot_password_user_outlet', 'API\UserController@forgot_password_user_outlet');
Route::post('outlet/otp_verifiy_user_outlet', 'API\UserController@otp_verifiy_user_outlet');
Route::post('outlet/update_password_user_outlet', 'API\UserController@update_password_user_outlet');

//route api for master user
Route::get('user/get_user_all', 'API\UserController@get_user_all');
Route::get('user/get_user_hotel', 'API\UserController@get_user_hotel');
Route::post('user/add_user', 'API\UserController@add_user');
Route::post('user/edit_user', 'API\UserController@edit_user');
Route::post('user/delete_user', 'API\UserController@delete_user');

//route for add token firebase device
Route::post('user/add_token_firebase', 'API\UserController@add_token_firebase_device');


Route::middleware(['auth:sanctum'])->group( function () {
    Route::post('user/change_password', 'API\UserController@change_password_user_outlet');
    Route::post('outlet/singout', 'API\UserController@logout_user_outlet');
    Route::post('user/member_activation', 'API\UserController@resend_verify');
});


Route::get('user/get_total_notif', 'API\UserController@get_user_total_notification');
