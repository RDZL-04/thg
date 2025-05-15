<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','hotel_id','created_by',
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

    public static function get_hotel_user($request){
        
        $result = HotelUser::where('hotel_id', $request['id_hotel'])
                ->join('users', 'users.id', '=', 'hotel_users.user_id')
                ->join('mroles', 'mroles.id', '=', 'users.id_role')
                ->select(
                        'hotel_users.id',
                        'users.full_name',
                        'users.email',
                        'mroles.role_nm',
                        'hotel_users.created_at',
                        'hotel_users.created_by',
                        )
                ->orderBy('mroles.id','ASC')
                ->get();
        return $result;
    }

    /*
	 * Function: Add data hotel user
	 * Param: data hotel user
	 *	$request	: 
	 */
    public static function add_user($request){
        $result= HotelUser::create([
                            'user_id' => $request['user_id'],
                            'hotel_id' => $request['hotel_id'],
                            'created_by' => $request['created_by'],
                                ] );
        return $result;
    }

     /*
	 * Function: Get user_id from hotel_users tables
	 * Param: hotel_user_id
	 *	$request	: 
	 */
    public static function get_hotel_user_id($request){
        $result = HotelUser::where('hotel_users.id', '=', $request)
                            ->join('users', 'users.id', '=', 'hotel_users.user_id')
                            ->join('mroles', 'mroles.id', '=', 'users.id_role')
                            ->select(
                                    'hotel_users.id as hotel_users_id',
                                    'hotel_users.user_id as user_id',
                                    'users.full_name',
                                    'users.email',
                                    'mroles.role_nm',
                                    'hotel_users.created_at',
                                    'hotel_users.created_by',
                                    )
                            ->get();
        return $result;
    }
}
