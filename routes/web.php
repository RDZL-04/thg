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

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['session'])->group( function () {
    Route::get('/', 'WEB\UserController@dashboard')->name('home');
    Route::get('home','WEB\UserController@dashboard')->name('home');
    Route::match(['GET','POST'], 'logout', 'WEB\UserController@logout')->name('logout');
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');
Route::post('otp', 'WEB\usercontroller@index')->name('otp');

// Route::post('cekotpregister/', 'API\UserController@cekotpregister');
Route::get('activation', 'API\UserController@verify')->name('verify');

Route::get('login_user','WEB\UserController@index')->name('login_user');
Route::post('login_user','WEB\UserController@action_login')->name('login_user.store');
Route::middleware('log.route')->post('webhook', WEB\WebhookController::class);
// Route::middleware('log.route')->get('mpccallback', WEB\MPCCallbackController::class);
Route::get('mpccallback', WEB\MPCCallbackController::class);
Route::middleware('log.route')->get('editprofilecallback', WEB\EditProfileCallback::class);
// Route::post('webhook', API\WebhookController::class);

// Forgot Password User Outlet
Route::get('forgot-password','WEB\UserController@forgot_password')->name('forgot_password');
Route::post('forgot_pass_send_email','WEB\UserController@forgot_pass_send_email')->name('send_email');
Route::post('forgot_pass_resend_email','WEB\UserController@forgot_pass_resend_email')->name('resend_email');
Route::post('forgot_pass_input_otp','WEB\UserController@forgot_pass_input_otp')->name('input_otp');
Route::post('forgot_pass_check_otp','WEB\UserController@forgot_pass_check_otp')->name('check_otp');
Route::post('forgot_pass_input_password','WEB\UserController@forgot_pass_input_password')->name('input_password');
Route::post('forgot_pass_reset_password','WEB\UserController@forgot_pass_reset_password')->name('reset_password');

// Get SessionId
Route::middleware('log.route')->get('getsessionid', WEB\GetSessionController::class);