<?php
/*
* Model RequestProposal
* author : rangga.muharam@arkamaya.co.id
*	
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestProposal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'submit_proposal';
    protected $fillable = [
        'hall_id','full_name','capacity',
        'email','phone', 'proposed_dt',
        'additional_request'
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
	 * Function: add data Request Proposal
	 * Param: 
	 *	$request	:
	 */
    public static function add_request_proposal($request){
        $result = RequestProposal::create([
                                'hall_id' => $request['hall_id'],
                                'full_name' => $request['full_name'],
                                'capacity' => $request['capacity'],
                                'email' => $request['email'],
                                'phone' => $request['phone'],
                                'proposed_dt' => $request['proposed_dt'],
                                'additional_request' => $request['additional_request'],
                              ]);
        return $result;
    }

}
