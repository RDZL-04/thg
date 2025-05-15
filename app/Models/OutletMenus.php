<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class OutletMenus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlet_menus';
    protected $fillable = [
        'name','fboutlet_id',
        'description','price',
        'images', 'menu_sts',
        'menu_cat_id', 'seq_no',
        'is_promo',
        'created_by', 'changed_by'
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
        * Function: Get Menu All based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_menus($request){
        
        $result =OutletMenus::where('fboutlet_menus.fboutlet_id', $request)
                //->where('deleted_at', '=', null)
                ->select('fboutlet_menus.*', 'fboutlet_mn_categories.name as cat_name')
                ->join('fboutlet_mn_categories','fboutlet_mn_categories.id','=','fboutlet_menus.menu_cat_id')
                ->orderBy('fboutlet_menus.menu_cat_id','ASC')
                ->orderBy('id','ASC')
                ->get();
        return $result;
    }

    /*
        * Function: Get Menu All based on fboutlet_id and category
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_menus_category($request){
        // dd($request['id_outlet']);
        $result =OutletMenus::where('fboutlet_id', $request['id_outlet'])
                ->where('menu_cat_id', '=', $request['id_category'])
                ->where('menu_sts', 1)
                ->select('id','name','description','price',
                'images', 'menu_sts',
                'menu_cat_id', 'seq_no',
                'is_promo',)
                ->get();
        return $result;
    }

    /*
        * Function: Get Menu All based on fboutlet_id
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_menus_by_outlet($id_outlet){
        // dd($request['id_outlet']);
        $result =OutletMenus::where('fboutlet_id', $id_outlet)
                ->where('menu_sts', 1)
                ->select('id','name','description','price',
                'images', 'menu_sts',
                'menu_cat_id', 'seq_no',
                'is_promo',)
                ->get();
        return $result;
    }

     /*
        * Function: Get Menu All based on fboutlet_id and category with image msystem 
        * Param: fboutlet_id
        * $request	: 
    */
    public static function get_menus_category_image($request){
        // dd($request['id_outlet']);
        $result = DB::select(DB::raw("SELECT id,NAME,description,price,
                            (CASE WHEN COALESCE(images,'') = '' THEN ( SELECT system_img FROM msystems WHERE system_type = 'default_img_dining' ) ELSE images END ) AS images,
                                menu_sts,menu_cat_id,seq_no,is_promo 
                            FROM
                                fboutlet_menus 
                            WHERE
                                fboutlet_id = '{$request['id_outlet']}' 
                                AND menu_cat_id = '{$request['id_category']}'
                                AND menu_sts = 1
                            SELECT id,NAME,description,price,
                            ( CASE WHEN COALESCE(images,'') = '' THEN ( SELECT system_img FROM msystems WHERE system_type = 'default_img_dining' ) ELSE images END ) AS images,
                            menu_sts,menu_cat_id,seq_no,is_promo 
                            FROM
                                fboutlet_menus 
                            WHERE
                                fboutlet_id = '{$request['id_outlet']}' 
                                AND menu_cat_id = '{$request['id_category']}')"));
        return $result;
    }
    /*
        * Function: Get Menu Detail based on menu_id
        * Param: menu_id
        * $request	: 
    */
    public static function get_menu_detail($request){
        
        $result =OutletMenus::where('fboutlet_menus.id', $request)
                ->select('fboutlet_menus.*', 'fboutlet_mn_categories.name as cat_name')
                // ->where('deleted_at', '=', null)
                ->join('fboutlet_mn_categories','fboutlet_mn_categories.id','=','fboutlet_menus.menu_cat_id')
                ->orderBy('id','ASC')
                ->get();
        return $result;
    }

    /*
        * Function: Get Menu Detail based on menu_id
        * Param: menu_id
        * $request	: 
    */
    public static function get_menu_detail_all($request){
        if($request == null ){
            $result =OutletMenus::select('fboutlet_menus.*', 'fboutlet_mn_categories.name as cat_name')
                // ->where('deleted_at', '=', null)
                ->join('fboutlet_mn_categories','fboutlet_mn_categories.id','=','fboutlet_menus.menu_cat_id')
                ->orderBy('id','ASC')
                ->get();
        }else{
            $result =OutletMenus::where('fboutlet_menus.id', $request)
                    ->select('fboutlet_menus.*', 'fboutlet_mn_categories.name as cat_name')
                    // ->where('deleted_at', '=', null)
                    ->join('fboutlet_mn_categories','fboutlet_mn_categories.id','=','fboutlet_menus.menu_cat_id')
                    ->orderBy('id','ASC')
                    ->get();
        }
        return $result;
    }

    /*
        * Function: Save menu - Add new Menu atau Edit Menu
        * Param: 
        * $request	: 
    */
    public static function save_menu($request)
    {
        //dd($request);
        if($request['images'] == null || $request['images'] == ''){
            $result= OutletMenus::updateOrCreate(['id' => $request['id']],
            [
               'fboutlet_id' => $request['fboutlet_id'],
               'name' => $request['name'],
               'description' => $request['description'],
               'price' => $request['price'],
               'menu_sts' => $request['menu_sts'],
               'menu_cat_id' => $request['menu_cat_id'],
               'seq_no' => $request['seq_no'],
               'is_promo' => $request['is_promo'],
               'changed_by' => $request['changed_by'],
               'created_by' => $request['created_by'],
            ] );
        }else{
            $result= OutletMenus::updateOrCreate(['id' => $request['id']],
                                             [
                                                'fboutlet_id' => $request['fboutlet_id'],
                                                'name' => $request['name'],
                                                'description' => $request['description'],
                                                'price' => $request['price'],
                                                'images' => $request['images'],
                                                'menu_sts' => $request['menu_sts'],
                                                'menu_cat_id' => $request['menu_cat_id'],
                                                'seq_no' => $request['seq_no'],
                                                'is_promo' => $request['is_promo'],
                                                'changed_by' => $request['changed_by'],
                                                'created_by' => $request['created_by'],
                                             ] );
        }
        
        return $result;
    }

    /*
        * Function: Update Promo sebagai 1 atau 0
        * Param: 
        * $request	: 
    */
    public static function update_promo($request)
    {
        //dd($request);
        $result= OutletMenus::update(['id' => $request['id']],
                                             [
                                                'fboutlet_id' => $request['fboutlet_id'],
                                                'name' => $request['name'],
                                                'description' => $request['description'],
                                                'price' => $request['price'],
                                                'images' => $request['images'],
                                                'menu_sts' => $request['menu_sts'],
                                                'menu_cat_id' => $request['menu_cat_id'],
                                                'seq_no' => $request['seq_no'],
                                                'is_promo' => $request['is_promo'],
                                                'changed_by' => $request['changed_by'],
                                                'created_by' => $request['created_by'],
                                             ] );
        return $result;
    }
}
