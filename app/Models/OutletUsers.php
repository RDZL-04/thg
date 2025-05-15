<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use DB;

class OutletUsers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlet_users';
    protected $fillable = [
        'user_id','fboutlet_id',
        'created_by', 'updated_by','active'
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
        * Function: Get Outlet User based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_outlet_user_id($request){
        
        $result = OutletUsers::where('fboutlet_users.user_id', $request)
                ->select('fboutlet_id','fboutlets.name as name_outlet')
                ->join('fboutlets','fboutlets.id','=','fboutlet_users.fboutlet_id') 
                ->get();
        return $result;
    }

    /*
        * Function: Get Outlet User based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_outlet_user($request){
        
        $result = OutletUsers::where('fboutlet_users.fboutlet_id', $request)
                ->select('fboutlet_users.*', 'users.full_name', 'mroles.role_nm','users.token')
                ->join('users','users.id','=','fboutlet_users.user_id')
                ->join('mroles','mroles.id','=','users.id_role')
                ->orderBy('users.id_role','ASC')
                ->orderBy('fboutlet_users.id','ASC')
                ->get();
        return $result;
    }

    /*
        * Function: Delete User from Outlet Users based from fboutlet_id and user_id
        * Param: 
        * $request	: 
    */
    public static function delete_outlet_user_from_hotel_id($request){
        $result = OutletUsers::select('fboutlet_users.*', 'users.full_name', 'mroles.role_nm','users.token')
                ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_users.fboutlet_id')
                ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                ->join('users','users.id','=','fboutlet_users.user_id')
                ->join('mroles','mroles.id','=','users.id_role')
                ->where('fboutlet_users.fboutlet_id', '=', $request['fboutlet_id'])
                ->where('fboutlet_users.user_id', '=', $request['user_id'])
                ->where('fboutlet_users.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->where('users.deleted_at', '=', null)
                ->orderBy('fboutlet_users.id','ASC')
                ->delete();
        return $result;
    }


    /*
        * Function: Get Outlet User based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_outlet_user_login($request){
        
        $result = OutletUsers::where('fboutlet_users.fboutlet_id', $request)
                ->where('active',1)
                ->select('fboutlet_users.*', 'users.full_name', 'mroles.role_nm','users.token')
                ->join('users','users.id','=','fboutlet_users.user_id')
                ->join('mroles','mroles.id','=','users.id_role')
                ->orderBy('fboutlet_users.id','ASC')
                ->get();
        return $result;
    }
    

     /*
        * Function: Get Outlet User Available based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_outlet_user_avail($request){
        $array = DB::table('fboutlet_users')->select('user_id')
                ->where('fboutlet_id', '=', $request)
                ->where('deleted_at', '=', null)
                ->get();
        if(count($array)> 0){
            foreach ($array as $value) {
                $arrayId[] = $value->user_id;
            }
        } else {
            $arrayId=[];
        }
		
		//add by kaka : 29/04/21 03:56pm
		//only specific roles permited
		$roles = Msystem::where('system_type', 'user_outlet')->where('system_cd', 'role_group')->first();
		if($roles) {
			$roles = explode(';', $roles->system_value);
		}else{
			$roles = [];
		}
		
        $result = User::select('users.id', 'users.full_name', 'users.email', 'mroles.role_nm')
                        ->rightJoin('mroles','mroles.id','=','users.id_role')
                        // ->where('fboutlet_users.fboutlet_id','<>', $request)
                        ->whereNotIn('users.id', $arrayId)
                        ->where('users.id', '<>',null)
						->whereIn('mroles.role_nm', $roles)
                        ->orderBy('users.id','ASC')
                        ->get();
        return $result;
    }

     /*
        * Function: Save Outlet User - Add user to outlet
        * Param: 
        * $request	: 
    */
    public static function save_outlet_user($request)
    {
        //dd($request);
        $result= OutletUsers::create([
                                        'fboutlet_id' => $request['fboutlet_id'],
                                        'user_id' => $request['user_id'],
                                        'created_by' => $request['created_by']
                                    ]);
        return $result;
    }
    
     /*
        * Function: delete_outlet_user
        * Param: id => fboutlet_users
        * $request	: 
    */
    public static function delete_outlet_user($request){
        $array = DB::table('fboutlet_users')->select('user_id')
                ->where('fboutlet_id', '=', $request)
                ->where('deleted_at', '=', null)
                ->get();
        foreach ($array as $value) {
            $arrayId[] = $value->user_id;
        }
        $result = User::select('users.id', 'users.full_name', 'mroles.role_nm')
                        ->rightJoin('mroles','mroles.id','=','users.id_role')
                        // ->where('fboutlet_users.fboutlet_id','<>', $request)
                        ->whereNotIn('users.id', $arrayId)
                        ->orderBy('users.id','ASC')
                        ->get();
        return $result;
    }

    /*
        * Function: Update active outlet
        * Param: id => fboutlet_users, id_outlet
        * $request	: 
    */
    public static function active_outlet($id_user, $id_outlet){
        $update= OutletUsers::where('user_id', $id_user)
                            ->update([
                                'active' => 0]);
        $result= OutletUsers::where('user_id', $id_user)
                            ->where('fboutlet_id', $id_outlet)
                            ->update([
                                'active' => 1]);
        return $result;
    }

    /*
        * Function: Update active outlet
        * Param: id => fboutlet_users, id_outlet
        * $request	: 
    */
    public static function unactivated_outlet($id_user){
        $update= OutletUsers::where('user_id', $id_user)
                            ->update([
                                'active' => 0]);
        return $update;
    }


}
