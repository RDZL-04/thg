<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MenuSideDish extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlet_mn_sdishs';
    protected $fillable = [
        'fboutlet_mn_id',
        'fboutlet_mn_sdish_id',
        'is_sidedish', 'created_by', 'updated_by'
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
    * request	: 
    */
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('d, M Y H:i');
    }

    /*
    * Function: Get SideDish menu based from 
    * Param: 
    * request	: 
    */
    public static function get_menu_sidedish($request){
        $result = DB::select("SELECT A.id,A.name, B.id AS sdish_id, B.is_sidedish,B.created_by, B.created_at, B.updated_by, B.updated_at
                    FROM fboutlet_menus A
                    LEFT JOIN fboutlet_mn_sdishs B 
                    ON A.id = B.fboutlet_mn_sdish_id
                    WHERE A.fboutlet_id = '$request->fboutlet_id'
                    and B.fboutlet_mn_id = '$request->menu_id'
                    AND A.deleted_at IS NULL
                    AND EXISTS (SELECT id FROM fboutlet_mn_sdishs B WHERE B.fboutlet_mn_sdish_id = A.id)");
        return $result;
    }

    /*
    * Function: Get SideDish menu based from menu
    * Param: 
    * request	: 
    */
    public static function get_sidedish_menu($request){
        // dd($request);
        $result = MenuSideDish::Where('fboutlet_mn_id',$request)
                                ->select('fboutlet_menus.name as name_sdishs','fboutlet_menus.price as price','fboutlet_menus.is_promo as is_promo_sdishs','fboutlet_mn_id',
                                'fboutlet_mn_sdish_id',
                                'is_sidedish')
                                ->join('fboutlet_menus','fboutlet_menus.id','=','fboutlet_mn_sdishs.fboutlet_mn_sdish_id')
                                ->get();
        return $result;
    }

    /*
    * Function: Get SideDish menu all
    * Param: 
    * request	: 
    */
    public static function get_sidedish_menu_all(){
        // dd($request);
        $result = MenuSideDish::select('fboutlet_menus.name as name_sdishs','fboutlet_menus.price as price','fboutlet_menus.is_promo as is_promo_sdishs','fboutlet_mn_id',
                                'fboutlet_mn_sdish_id',
                                'is_sidedish')
                                ->join('fboutlet_menus','fboutlet_menus.id','=','fboutlet_mn_sdishs.fboutlet_mn_sdish_id')
                                ->get();
        return $result;
    }

     /*
    * Function: Get SideDish yang belum di add
    * Param: 
    * request	: 
    */
    public static function get_sidedish($request){
        //dd($request->menu_id);
        $result = DB::select("SELECT A.id, A.name
                    FROM fboutlet_menus A
                    WHERE fboutlet_id = '$request->fboutlet_id'
                    -- AND menu_cat_id = '$request->menu_cat_id'
                    AND NOT EXISTS (SELECT id FROM fboutlet_mn_sdishs B WHERE B.fboutlet_mn_sdish_id = A.id AND B.fboutlet_mn_id = '".$request->menu_id."')
                    AND id <> '$request->menu_id'
                    AND A.deleted_at IS NULL
                    ");
        return $result;
    }

     /*
    * Function: Get SideDish yang belum di add
    * Param: 
    * request	: 
    */
    public static function get_sidedish_menu_cat($request){
        //dd($request->menu_id);
        $result = DB::select("SELECT A.id, A.name
                    FROM fboutlet_menus A
                    WHERE fboutlet_id = '$request->fboutlet_id'
                    AND menu_cat_id = '$request->menu_cat_id'
                    AND NOT EXISTS (SELECT id FROM fboutlet_mn_sdishs B WHERE B.fboutlet_mn_sdish_id = A.id AND B.fboutlet_mn_id = '".$request->menu_id."')
                    AND id <> '$request->menu_id'
                    AND A.menu_sts = 1
                    AND A.deleted_at IS NULL
                    ");
        return $result;
    }

      /*
    * Function: Add SideDish Menu
    * Param: 
    * request	: 
    */
    public static function add_sidedish($request){
        //dd($request);
        $result= MenuSideDish::updateOrCreate(['id' => $request['id']],
                                             [
                                                'fboutlet_mn_id' => $request['fboutlet_mn_id'],
                                                'fboutlet_mn_sdish_id' => $request['fboutlet_mn_sdish_id'],
                                                'is_sidedish' => $request['is_sidedish'],
                                                'created_by' => $request['created_by'],
                                             ] );
        return $result;
    }

    /*
    * Function: Add SideDish Menu
    * Param: 
    * request	: 
    */
    public static function delete_sidedish($request){
        $result = MenuSideDish::find($request->sidedish_id);     
        $result->forceDelete();
        return $result;
    }


}
