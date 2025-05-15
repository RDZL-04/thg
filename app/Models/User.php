<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
// use Laravel\Jetstream\HasProfilePhoto;
// use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Msystem;
use App\Models\RoleAuth;
use Auth;

    /*
	 * Model User
	 * author : ilhammaulanpratama@arkamaya.co.id
	 *	
	 */

class User extends Authenticatable 
{
    use HasApiTokens;
    use HasFactory;
    use SoftDeletes;
    // use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email',
        'password','phone',
        'id_role','user_name','gender',
        'address','state_province','postal_cd',
        'country','city','image','date_of_birth',
        'created_by','updated_by','token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'image',
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

    // 

    /*
	 * Function: check akun user from table user
 	 * Param: 
	 *	$request	: $email, $phone
	 */
    public static function CheckUser($request){
        // dd($request);
        // $result= DB::select(DB::raw("select * from users where phone = '{$phone}' "));
        $result = User::whereRaw("BINARY user_name = '$request'")->first();
        return $result;
    }

    /*
	 * Function: check akun user from table user
 	 * Param: 
	 *	$request	: $email, $phone
	 */
    public static function check_user( $phone){
        // $result= DB::select(DB::raw("select * from users where phone = '{$phone}' "));
        $result = User::where('phone', $phone)->first();
        return $result;
    }
    /*
	 * Function: change email_verified to 0
	 * Param: 
	 *	$request	: $phone
	 */
    public static function verified($phone)
    {
        $result = User::where('phone', $phone)
                                ->update([
                                        'email_verified' => 0, 
                                        ]);
        return $result;
    }
    /*
	 * Function: update profil
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_user($request)
    {
        $result= User::where('id', $request['id'])
                                    ->update( [
                                                'full_name' => $request['full_name'],
                                                // 'email' => $request['email'], 
                                                'gender' => $request['gender'],
                                                'address' => $request['address'],
                                                'city' => $request['city'],
                                                'country' => $request['country'],
                                                'state_province' => $request['state_province'],
                                                'postal_cd' => $request['postal_cd'],
                                                'phone' => $request['phone'],
                                                'date_of_birth' => $request['date_of_birth'],
                                             ] );
        return $result;
    }

    /*
	 * Function: update token user
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_token($request)
    {   
        $result= User::where('id', $request['id'])
                                    ->update( [
                                                'token' => $request['token']
                                             ] );
        return $result;
    }

    /*
	 * Function: get data all User
	 * Param: 
	 *	$request	: 
	 */
    public static function get_user_all(){
        // $result =User::orderBy('full_name','DESC')
        // ->get();
        $result =User::join('mroles', 'mroles.id', '=', 'users.id_role')
               ->select('users.*',
                        'mroles.role_nm'
                        )
                ->orderBy('users.id_role','ASC')
                ->orderBy('users.created_at','DESC')
                ->get();
        // return $result;
    return $result;
}

	/*
	 * Function: get data all User Hotel
	 * Param: 
	 *	$request	: 
	 */
    public static function get_user_hotel(){
        // $result =User::orderBy('full_name','DESC')
        // ->get();
		$roles = Msystem::where('system_type', 'user_hotel')->where('system_cd', 'role_group')->first();
		if($roles) {
			$roles = explode(';', $roles->system_value);
		}else{
			$roles = [];
		}
		
        $result =User::join('mroles', 'mroles.id', '=', 'users.id_role')
               ->select('users.*',
                        'mroles.role_nm'
                        )
                ->whereIn('mroles.role_nm', $roles)
                ->get();
        // return $result;
    return $result;
}

    /*
	 * Function: get data all User
	 * Param: 
	 *	$request	: 
	 */
public static function get_user_id($request)
    {
        // dd($request);
        $result= User::join('mroles', 'mroles.id', '=', 'users.id_role')
                    ->select('users.*','mroles.role_nm')
                    ->where('users.id', $request)
                    ->get();
        return $result;
    }
    /*
	 * Function: save data user
	 * Param: 
	 *	$request	: 
	 */
    public static function add_user($request){
        $password =  bcrypt($request['password']);
        $result= User::create( [
                                'full_name' => $request['full_name'],
                                'email' => $request['email'], 
                                'user_name' => $request['user_name'],
                                'phone' => $request['phone'],
                                'id_role' => $request['id_role'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by'],
                                'password' => $password,
                             ] );
    return $result;
    }

    /*
	 * Function: edit data user
	 * Param: 
	 *	$request	: 
	 */
    public static function edit_user($request){
        $result= User::where('id', $request['id'])
                            ->update( [
                                'full_name' => $request['full_name'],
                                'email' => $request['email'], 
                                'user_name' => $request['user_name'],
                                'phone' => $request['phone'],
                                'id_role' => $request['id_role'],
                                'updated_by' => $request['updated_by'],
                                
                             ] );
    return $result;
    }   
    /*
	 * Function: save image user
	 * Param: 
	 *	$request	: 
	 */
    public static function update_images($request){
        // dd($request->all());
        $result= User::where('id', $request['id'])
                            ->update( [
                                'image' => $request['image']
                             ] );
    return $result;
    }
	
	//add by Arkra (arif@arkamaya.co.id)
	public function permission($ability)
	{
		return RoleAuth::where('role_id', Auth::user()->id_role)->where('permission_name', $ability)->first();
	}
	
}
