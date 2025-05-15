<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

    /*
	 * Model apilaction for api key
	 * author : ilhammaulanpratama@arkamaya.co.id
	 *	
	 */

class Application extends Model
{
    use HasFactory;

    /*
	 * Function: check api key
	 * Param: 
	 *	$request	: $api
	 */
    public static function cek_api_key($api){
        $result= DB::select(DB::raw("select * from applications where api_key = '{$api}'"));
        return $result;
    }
}
