<?php
/*
* Model Hall
* author : rangga.muharam@arkamaya.co.id
*	
*/


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HallImages extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hall_images';
    protected $fillable = [
        'hall_id','name','filename',
        'seq','status',
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
    public static function add_hall_images($request){
        $result = HallImages::create([
                                'hall_id' => $request['hall_id'],
                                'name' => $request['name'],
                                'filename' => $request['filename'],
                                'seq' => $request['seq'],
                                'status' => $request['status'],
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
    public static function update_hall_images($request)
    {
        if(!empty($request['created_by'])){
            $result= HallImages::updateOrCreate(['id' => $request['id']],
                                    [
                                        'hall_id' => $request['hall_id'],
                                        'name' => $request['name'],
                                        'filename' => $request['filename'],
                                        'seq' => $request['seq'],
                                        'status' => $request['status'],
                                        'created_by' => $request['created_by'],
                                        'updated_by' => $request['created_by']
                                    ]);
        } else {
            $result= HallImages::updateOrCreate(['id' => $request['id']],
                                    [
                                        'hall_id' => $request['hall_id'],
                                        'name' => $request['name'],
                                        'filename' => $request['filename'],
                                        'seq' => $request['seq'],
                                        'status' => $request['status'],
                                        'updated_by' => $request['updated_by']
                                    ]);
        }
        return $result;
    }

    /*
	 * Function: Get Image Halls
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_image($request){
        
        $result = HallImages::where('hall_id', $request)
                ->select('id',
                        'hall_id',
                        'name',
                        'filename',
                        'seq',
                        'status'
                        )
                ->orderBy('seq','ASC')
                ->get();
        return $result;
    }
    
    /*
	 * Function: Get Image Halls by seq
	 * Param: 
	 *	$request	: $data
	 */
    public static function get_image_by_seq($request){
        
        $result = HallImages::where('hall_id', $request)
                ->where('seq', 1)
                ->select(
                        'filename'
                        )
                ->first();
        return $result;
    }

}
