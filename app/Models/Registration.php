<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

    /*
	 * Model Registration
	 * author : ilhammaulanpratama@arkamaya.co.id
	 *	
	 */

class Registration extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'phone', 'email','otp','Phone_verified','password'
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

    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
 protected $hidden = [
        'password',
    ];

    /*
	 * Function: check akun user from table registration
	 * Param: 
	 *	$request	: $email, $phone
	 */
    public static function check_user( $phone){
        $result = Registration::where('phone', $phone)->first();
        return $result;
    }
    
    /*
	 * Function: check otp registration
	 * Param: 
	 *	$request	: $otp, $phone
	 */
    public static function check_otp($phone,$otp)
    {
        $time = date("Y-m-d H:i:s", strtotime("-30 Second"));
        // $result= DB::select(DB::raw("select * from registrations where phone = '{$phone}' AND otp = '{$otp}' AND phone_verified = '0' AND '{$time}' < updated_at "));
        $result= DB::select(DB::raw("select * from registrations where phone = '{$phone}' AND otp = '{$otp}' AND phone_verified = '0' "));
        // $result = Registration::where('phone', $phone)
        //                         ->where('otp', $otp)
        //                         ->where('phone_verified' , 0)
        //                         ->where('updated_at', '<',$time )
        //                         ->first();
        return $result;
        
    }

     /*
	 * Function: change phone_verified to 0
	 * Param: 
	 *	$request	: $phone
	 */
    public static function phone_verified($phone)
    {
        $result = Registration::where('phone', $phone)
        ->update(['phone_verified' => 0]);
        return $result;
    }

    /*
	 * Function: change phone_verified to 1
	 * Param: 
	 *	$request	: $phone
	 */
    public static function verified($phone)
    {
        $result = Registration::where('phone', $phone)
        ->update(['phone_verified' => 1]);
        return $result;
    }

    /*
	 * Function: update or create data user registration
	 * Param: 
	 *	$request	: $request [phone, full_name,email, password, otp]
	 */
    public static function update_user_regist($request)
    {
        $password =  bcrypt($request['password']);
        $result= Registration::updateOrCreate(['phone' => $request['phone']],
                                             [
                                                'email' => $request['email'],
                                                'full_name' => $request['full_name'],
                                                'password' => $password,
                                                'otp' => $request['otp'],
                                                'phone_verified' => 0
                                             ] );
        return $result;
    }
}
