<?php
/*
* Model Hall
* author : moh.arif.rifai@arkamaya.co.id
*	
* 03 Juni 2022
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForgotPasswordCnt extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'forgot_password_cnt';
    protected $fillable = [
        'user_id','req_dt','cnt','created_dt','update_dt'
    ];

     /*
	 * Function: add data Halls
	 * Param: 
	 *	$request	:
	 */
    public static function add_forgot_password_cnt($request){
        $result = ForgotPasswordCnt::create(
                    [
                    'user_id' => $request['user_id'],
                    'req_dt' => \Carbon\Carbon::now()->format('Y-m-d'),
                    'cnt' => $request['cnt'],
                    ]);
        return $result;
    }

    public static function update_forgot_password_cnt($request)
    {
        $result = ForgotPasswordCnt::where('user_id', $request['user_id'])
        ->update([
            'cnt' => $request['cnt'],
        ]);
        return $result;
    }

}
