<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can Profile API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['language'])->group( function ($locale) {
   
   
   Route::post('fmc', 'API\ProfileController@fmc');

    Route::middleware('auth:sanctum')->group( function () {
        Route::post('profil/change_password', 'API\ProfileController@change_password');
        Route::post('profil/get_stay', 'API\ProfileController@get_stay');
        Route::post('profil/get_profile', 'API\ProfileController@get_profile');
        Route::post('profil/edit_profile', 'API\ProfileController@edit_profile');
        Route::post('profil/add_image', 'API\ProfileController@add_image');
        Route::post('profil/singout', 'API\ProfileController@logout');
});
Route::get('profil/get_dining', 'API\DiningController@get_order_dining_member');
});