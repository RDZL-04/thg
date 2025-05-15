<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Members extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'members';
    protected $fillable = [
        'fullname', 'email','phone',
         'id_role','date_of_birth',
         'email_verified','id_type', 
         'id_no','gender', 'mdcid',
         'image','city',
         'country','state_province',
         'postal_cd','address',
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
	 * Function: save data member
	 * Param: 
	 *	$request	: 
	 */
    public static function add_member($request){
        $result = Members::create([
                                'mdcid' => $request['mdcid'],
                                'fullname' => $request['full_name'],
                                'email' => $request['email'], 
                                'phone' => $request['phone']
                             ]);
        return $result;
    }

    /*
	 * Function: edit data Member
	 * Param: 
	 *	$request	: 
	*/
    public static function save_member($request){
        $result = Members::where('mdcid', '=', $request['mdcid'])
            ->withTrashed()
            ->update([
                'fullname' => $request['full_name'],
                'email' => $request['email'], 
                'phone' => $request['phone'],
                'device_id' => $request['deviceId']
            ]);
        return $result;
    }   

    /*
	 * Function: edit data Member
	 * Param: 
	 *	$request	: 
	*/
    public static function edit_member($request){
        $result = Members::where('id', '=', $request['id'])
                           ->update([
                                'date_of_birth' => $request['date_of_birth'],
                                'id_type' => $request['id_type'], 
                                'id_no' => $request['id_no'],
                                'gender' => $request['gender'],
                                'image' => $request['image'],
                                'city' => $request['city'],
                                'country' => $request['country'],
                                'state_province' => $request['state_province'],
                                'postal_cd' => $request['postal_cd'],
                                'address' => $request['address'],
                            ]);
        return $result;
    }   

    public static function get_profile($request){
        
        $result =Members::where('members.id', $request)
                ->leftJoin('msystems', 'msystems.system_cd', '=', 'members.gender')
                ->where('msystems.system_type','gender')
                ->select(
                        'members.id',
                        'members.fullname',
                        'members.email',
                        'members.phone',
                        'members.date_of_birth',
                        'members.id_type',
                        'members.id_no',
                        'members.image',
                        'members.city',
                        'members.country',
                        'members.state_province',
                        'members.postal_cd',
                        'members.address',
                        'msystems.system_value as gender',
                        )
                ->first();
        return $result;
    }
}
