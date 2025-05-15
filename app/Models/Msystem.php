<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Msystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_type','system_cd','system_value','created_by','updated_by','system_img'
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
	 * Function: get data all msystem
	 * Param: 
	 *	$request	: 
	 */
    public static function getData(){
        // dd('ada');
        $result =Msystem::orderBy('created_at','DESC')
        ->get();
    return $result;
    }

    /*
	 * Function: get data System search by type and cd
	 * body: 
	 * $request	:
	 **/
    public static function get_system_type_cd($request)
    {
            $result = Msystem::where('system_type','LIKE','%'.$request['system_type'].'%')
                            ->where('system_cd','LIKE','%'.$request['system_cd'].'%')
                            ->get();
            
            return $result;
        }

    /*
	 * Function: Add data msystem
	 * Param: data system
	 *	$request	: 
	 */
    public static function add_system($request){
        $result= Msystem::create([
                                        'system_type' => $request['system_type'],
                                        'system_cd' => $request['system_cd'],
                                        'system_value' => $request['system_value'],
                                        'system_img' => $request['system_img'],
                                        'created_by' => $request['created_by'],
                                        'updated_by' => $request['created_by'],
                                             ] );
        return $result;
    }

    /*
	 * Function: update system
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_system($request)
    {
        
        if(!empty($request['system_img'])){
            $result= Msystem::where('id', $request['id'])
                            ->update( [
                                        'system_type' => $request['system_type'],
                                        'system_cd' => $request['system_cd'],
                                        'system_value' => $request['system_value'],
                                        'system_img' => $request['system_img'],
                                        'updated_by' => $request['updated_by'],    
                                    ] );
        }else{
            $result= Msystem::where('id', $request['id'])
                            ->update( [
                                        'system_type' => $request['system_type'],
                                        'system_cd' => $request['system_cd'],
                                        'system_value' => $request['system_value'],
                                        'updated_by' => $request['updated_by'],    
                                    ] );
        }
        
        return $result;
    }

    public static function getCountry()
    {
        $result = Msystem::where('system_type','country')
                            ->orderBy('system_value','ASC')
                            ->get();
        return $result;
    }


    public static function get_payment_source()
    {
        $result = Msystem::where('system_type','payment_source')
                            ->orderBy('system_value','ASC')
                            ->get();
        return $result;
    }

    public static function get_gender()
    {
        $result = Msystem::where('system_type','gender')
                            ->orderBy('system_value','ASC')
                            ->get();
        return $result;
    }

    public static function get_promo()
    {
        $result = Msystem::where('system_type','be_promotions')
                            ->where('system_cd','discountCode')
                            ->select('id',
                                    'system_type',
                                    'system_cd',
                                    'system_value',
                                    'system_img'
                            )
                            ->orderBy('system_value','ASC')
                            ->get();
        return $result;
    }

    public static function get_mice_category()
    {
        $result = Msystem::where('system_type','mice_category')
                            ->select(
                                    'system_cd',
                                    'system_value'
                            )
                            ->orderBy('system_cd','ASC')
                            ->get();
        return $result;
    }
}
