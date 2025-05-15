<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterRole extends Model
{
    use HasFactory;
    use SoftDeletes;
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
    
    protected $table = 'mroles';

    protected $fillable = [
        'role_nm','created_by',
        'updated_by','description'
    ];

    public static function get_role(){
        $result =MasterRole::orderBy('id','ASC')
        ->get();
    return $result;
    }

    public static function add_role($request){
        $result= MasterRole::create([
                                        'role_nm' => $request['role_nm'],
                                        'description' => $request['description'],
                                        'created_by' => $request['created_by'],
                                        'updated_by' => $request['created_by'],
                                             ] );
        return $result;
    }

    public static function edit_role($request){
        $result= MasterRole::where('id', $request['id'])
                            ->update([
                                    'role_nm' => $request['role_nm'],
                                    'description' => $request['description'],
                                    'updated_by' => $request['updated_by'],
                                     ] );
        if($result){
                return MasterRole::where('id', $request['id'])->get();
        }
        else{
            return $result;
        }
    }
}
