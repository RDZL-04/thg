<?php

//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Token Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API Token routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
| @ref: https://laravel.com/docs/8.x/validation#quick-defining-the-routes
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // return $request->user();
// });

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
	
	// Revoke all tokens...
	$user->tokens()->delete();

	// Revoke a specific token...
	//$user->tokens()->where('id', $id)->delete();

    return $user->createToken($request->device_name)->plainTextToken;
});