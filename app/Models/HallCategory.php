<?php
/*
* Model HallCategory
* author : rangga.muharam@arkamaya.co.id
*	
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HallCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hall_category';
    protected $fillable = [
        'mice_category_id','hall_id',
        'created_by', 'updated_by'
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
	 * Function: add data Halls
	 * Param: 
	 *	$request	:
	 */
    public static function add_hall_category($request){
        $result= HallCategory::create([
                                'mice_category_id' => $request['mice_category_id'],
                                'hall_id' => $request['hall_id'],
                                'created_by' => $request['created_by'],
                                'updated_by' => $request['created_by']
                                ]);
        return $result;
    }

     /*
	 * Function: update Halls
	 * Param: 
	 *	$request	: $data
	 */
    public static function update_hall_category($request)
    {
        $result= HallCategory::where('mice_category_id', $request['mice_category_id'])
                                    ->update( [
                                        'mice_category_id' => $request['mice_category_id'],
                                        'hall_id' => $request['hall_id'],
                                        'updated_by' => $request['updated_by']
                                    ]);
        return $result;
    }

}
