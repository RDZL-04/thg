<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OTP extends Model
{
    use HasFactory;
    protected $table = 'otps';
    protected $fillable = [
        'user_id', 'otp'
    ];

    /*
	 * Function: change format timestamp
	 * Param: 
	 *	$request	: 
	 */
    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->format('d, M Y H:i');
    }
    /*
        * Function: change format timestamp
        * Param: 
        *	$request	: 
        */
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('d, M Y H:i');
    }

    /*
	 * Function: create otp code for login user
	 * Param: 
	 *	$request	: $data[id,otp]
	 */
    public static function create_otp($data){
        $result = OTP::updateOrCreate(['user_id' => $data['id']],
                                        [
                                            'otp' => $data['otp'],
                                        ] );
        return $result;
    }

    /*
	 * Function: check otp for login user
	 * Param: 
	 *	$request	: $request[id,otp]
	 */
    public static function check_otp($request)
    {
        $otp = $request->otp;
        $id = $request->id;
        $time = date("Y-m-d H:i:s", strtotime("-30 Second"));
        // $result= DB::select(DB::raw("select * from otps where user_id = '{$id}' AND otp = '{$otp}' AND '{$time}' < updated_at "));
        $result= DB::select(DB::raw("select * from otps where user_id = '{$id}' AND otp = '{$otp}'"));
        return $result;
    }
}
