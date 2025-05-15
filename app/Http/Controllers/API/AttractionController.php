<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Attraction API Controller
|--------------------------------------------------------------------------
|
| Nearby Attraction Hotel API Controller.
| 
| @author: arif@arkamaya.co.id 
| @update: July 22, 2021 10:30 am
*/

class AttractionController extends Controller
{
	/**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function get_near_attraction()
	{
		return [];
	}
}