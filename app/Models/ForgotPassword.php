<?php
/*
* Model Hall
* author : rangga.muharam@arkamaya.co.id
*	
* 12 April 2021
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForgotPassword extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'forgot_password';
    protected $fillable = [
        'user_id','email','otp',
        'otp_verified',
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
	 * Function: add data Halls
	 * Param: 
	 *	$request	:
	 */
    public static function add_forgot_password($request){
        $result = ForgotPassword::create(
                            [
                            'user_id' => $request['user_id'],
                            'email' => $request['email'],
                            'otp' => $request['otp'],
                            'otp_verified' => 0
                            ]);
        return $result;
    }

    /*
	 * Function: change otp_verified to 0
	 * Param: 
	 *	$request	: $otp
	 */
    public static function reset_otp_verified($request)
    {
        $result = ForgotPassword::where('email', $request['email'])
        ->update([
            'otp' => $request['otp'],
            'otp_verified' => 0
        ]);
        return $result;
    }

    /*
	 * Function: change otp_verified to 1
	 * Param: 
	 *	$request	: $otp
	 */
    public static function otp_verified($email)
    {
        $result = ForgotPassword::where('email', $email)
        ->update(['otp_verified' => 1]);
        return $result;
    }

    /*
	 * Function:update hashing value
	 * Param: 
	 *	$request	: $phone
	 */
    public static function update_token($email,$token)
    {
        $result = ForgotPassword::where('email', $email)
        ->update(['token' => $token]);
        return $result;
    }

}
