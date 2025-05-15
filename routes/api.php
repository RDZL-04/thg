<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('getUser/{request}', 'API\RegisterController@index');
// Route::post('savetemp/', 'API\RegisterController@savetemp');


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['language'])->group( function () {
    // Route::post('lang', 'API\UserController@switchlang');
    // Route::post('lang', 'API\UserController@switch');
    Route::post('lang', 'API\UserController@switchlang');
});

Route::middleware('log.route')->post('inquiry', API\InquiryController::class);