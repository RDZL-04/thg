<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id', 'token'
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
    public static function save_token($request){
        $result= Notification::updateOrCreate(['device_id' => $request['device_id']],
                                             [
                                                'token' => $request['token'],
                                             ] );
        return $result;
    }
}
