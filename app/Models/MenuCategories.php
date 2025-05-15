<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MenuCategories extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fboutlet_mn_categories';
    protected $fillable = [
        'name', 'created_by', 'updated_by','seq_no', 'fboutlet_id','show_in_menu'
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
        * Function: Get all Categories Menu
        * Param: 
        *	$request	: 
    */
    public static function get_categories_all($request){
        // dd($request);
        if(!empty($request['fboutlet_id'])){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                        ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                        ->where('fboutlet_mn_categories.fboutlet_id', '=', $request['fboutlet_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                        ->orderBy('seq_no', 'ASC')
                        ->get();
        } else {
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                    ->where('fboutlets.deleted_at', '=', null)
                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                    ->orderBy('seq_no', 'ASC')
                    ->get();
        }
        return $result;
    }

    /*
        * Function: Get all Categories Menu
        * Param: 
        *	$request	: 
    */
    public static function get_categories_all_with_seq_no($request){
        // dd($request);
        if(!empty($request['fboutlet_id'])){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                        ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                        ->where('fboutlet_mn_categories.fboutlet_id', '=', $request['fboutlet_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        // ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                        ->orderBy('seq_no', 'ASC')
                        ->get();
        } else {
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                    ->where('fboutlets.deleted_at', '=', null)
                    // ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                    ->orderBy('seq_no', 'ASC')
                    ->get();
        }
        return $result;
    }

    /*
        * Function: Get all Categories Menu for User
        * Param: 
        *	$request	: 
    */
    public static function get_categories_all_user($request){
        if( (!empty($request['fboutlet_id']) && !empty($request['hotel_id'])) || (!empty($request['fboutlet_id'])) ){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                        ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.user_id', '=', $request['user_id'])
                        ->where('fboutlet_users.deleted_at', '=', null)
                        ->where('fboutlet_mn_categories.fboutlet_id', '=', $request['fboutlet_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                        ->orderBy('seq_no', 'ASC')
                        ->get();
        } elseif( !empty($request['hotel_id']) ){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                        ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                        ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                        ->where('fboutlet_users.user_id', '=', $request['user_id'])
                        ->where('fboutlet_users.deleted_at', '=', null)
                        ->where('fboutlets.hotel_id', '=', $request['hotel_id'])
                        ->where('fboutlets.deleted_at', '=', null)
                        ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                        ->orderBy('seq_no', 'ASC')
                        ->get();
        } else {
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name')->distinct()
                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                    ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                    ->where('fboutlets.deleted_at', '=', null)
                    ->where('fboutlet_users.user_id', '=', $request['user_id'])
                    ->where('fboutlet_users.deleted_at', '=', null)
                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                    ->orderBy('seq_no', 'ASC')
                    ->get();
        }
        return $result;
    }

    /*
        * Function: Get all Categories Menu with hotel_id
        * Param: 
        *	$request	: 
    */
    public static function get_menu_categories_all_hotel($request){
        // dd($request);
        if(!empty($request['hotel_id'])){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name', 'fboutlets.id as outlet_id')->distinct()
                                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                                    ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                    ->where('fboutlets.deleted_at', '=', null)
                                    ->where('hotels.id', '=', $request['hotel_id'])
                                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                                    ->orderBy('seq_no', 'ASC')
                                    ->get();
        } else {
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name', 'fboutlets.id as outlet_id')->distinct()
                                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                                    ->where('fboutlets.deleted_at', '=', null)
                                    ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                                    ->orderBy('seq_no', 'ASC')
                                    ->get();
        }
        return $result;
    }

    /*
        * Function: Get all Categories Menu for User
        * Param: 
        *	$request	: 
    */
    public static function get_menu_categories_all_user_hotel($request){
        if(!empty($request['hotel_id'])){
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name', 'fboutlets.id as outlet_id')->distinct()
                                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                                    ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                                    ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                    ->where('fboutlets.deleted_at', '=', null)
                                    ->where('fboutlets.status', '=', 1)
                                    ->where('fboutlet_users.user_id', '=', $request['user_id'])
                                    ->where('fboutlet_users.deleted_at', '=', null)
                                    ->where('hotels.id', '=', $request['hotel_id'])
                                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                                    ->orderBy('seq_no', 'ASC')
                                    ->get();
        } else {
            $result = MenuCategories::select('fboutlet_mn_categories.*', 'fboutlets.name as outlet_name', 'fboutlets.id as outlet_id')->distinct()
                                    ->join('fboutlets', 'fboutlets.id', '=', 'fboutlet_mn_categories.fboutlet_id')
                                    ->join('fboutlet_users', 'fboutlet_users.fboutlet_id', '=', 'fboutlets.id')
                                    ->join('hotels', 'hotels.id', '=', 'fboutlets.hotel_id')
                                    ->where('fboutlets.deleted_at', '=', null)
                                    ->where('fboutlets.status', '=', 1)
                                    ->where('fboutlet_users.user_id', '=', $request['user_id'])
                                    ->where('fboutlet_users.deleted_at', '=', null)
                                    ->orderBy('fboutlet_mn_categories.fboutlet_id', 'ASC')
                                    ->orderBy('seq_no', 'ASC')
                                    ->get();
        }
        return $result;
    }

    /*
      -  * Function: Get all Categories Menu by  outlet
        * Param: 
        *	$request	: 
    */
    public static function get_category_by_outlet($request){
        $result = MenuCategories::select('fboutlet_menus.menu_cat_id','fboutlet_mn_categories.name')
                                ->join('fboutlet_menus','fboutlet_menus.menu_cat_id','=','fboutlet_mn_categories.id')
                                ->where('fboutlet_menus.fboutlet_id','=',$request)
                                ->orderBy('fboutlet_mn_categories.seq_no','ASC')
                                ->groupBy('fboutlet_mn_categories.name','fboutlet_menus.menu_cat_id')
                                ->get();
        return $result;
    }
     /*
      -  * Function: Get all Categories Menu by  outlet
        * Param: 
        *	$request	: 
    */
    public static function get_category_by_outlet_scan($request){
        $result = MenuCategories::select('fboutlet_menus.menu_cat_id','fboutlet_mn_categories.name')
                                ->join('fboutlet_menus','fboutlet_menus.menu_cat_id','=','fboutlet_mn_categories.id')
                                ->where('fboutlet_menus.fboutlet_id','=',$request)
                                ->where('fboutlet_mn_categories.show_in_menu','=','1')
                                ->orderBy('fboutlet_mn_categories.seq_no','ASC')
                                ->groupBy('fboutlet_mn_categories.name','fboutlet_menus.menu_cat_id')
                                ->get();
        return $result;
    }

      /*
    * Function: Add or Edit Menu Category
    * Param: id (jika edit data), name, created_by (jika data baru) dan updated_by
    * request	: 
    */
    public static function add_menu_categories($request){
        if(!empty($request['id'])) {
            $result = MenuCategories::where('id', $request['id'])
                                    ->update([
                                            'name' => $request['name'],
                                            'seq_no' => $request['seq_no'],
                                            'fboutlet_id' => $request['fboutlet_id'],
                                            'show_in_menu' => $request['show_in_menu'],
                                            'updated_by' => $request['updated_by'],
                                    ]);
        } else {
            $result = MenuCategories::create([
                                            'name' => $request['name'],
                                            'seq_no' => $request['seq_no'],
                                            'fboutlet_id' => $request['fboutlet_id'],
                                            'show_in_menu' => $request['show_in_menu'],
                                            'created_by' => $request['created_by'],
                                            'updated_by' => $request['updated_by'],
                                    ]);
        }
        return $result;
    }

    /*
    * Function: Delete Menu Category
    * Param: id -> id menu category
    * request	: 
    */
    public static function delete_menu_categories($request){
        $result = MenuCategories::find($request->id);     
        $result->delete();
        return $result;
    }

}
