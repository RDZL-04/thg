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
    Route::post('proposal/save_request_proposal', 'API\RequestProposalController@save_request_proposal');
   });
