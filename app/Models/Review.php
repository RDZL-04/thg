<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Review extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'customer_id','transaction_no','review_type','rating_number','comment'
    ];
    /*
	 * Function: change format timestamp
	 * Param: 
	 *	$request	: 
	 */
    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['created_at'])
        ->format('Y-m-d H:i');
    }
    /*
        * Function: change format timestamp
        * Param: 
        *	$request	: 
        */
    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['updated_at'])
        ->format('Y-m-d H:i');
    }

    public static function get_ranting($request){
        // dd($request);
        $result= DB::select(DB::raw("select avg(rev.rating_number) as rating from reviews rev
                                    inner join reservations res on (rev.transaction_no=res.transaction_no)
                                    where res.hotel_id = '{$request}' and rev.review_type = 1"));
        return $result;
    }

    public static function get_top_review($request){
        // dd($request);
        $result= DB::select(DB::raw("SELECT reviews.id,reviews.rating_number,reviews.comment,guests.full_name as guest_full_name
                                        FROM reviews INNER JOIN reservations ON reviews.transaction_no = reservations.transaction_no
                                        INNER JOIN guests ON guests.id = reservations.customer_id
                                        WHERE reservations.hotel_id= '{$request}'
                                        AND reviews.review_type = 1
                                        ORDER BY 	reviews.rating_number DESC,
	                                                reviews.created_at DESC 
                                        LIMIT 0,5"));
                                    
        return $result;
    }

    public static function add_review($request){
        if(empty($request['id'])){
            $request['id'] = null;
        }
        if(empty($request['rating_number'])){
            $request['rating_number'] = null;
        }
        $result= Review::updateOrCreate(['id' => $request['id']],[
            'customer_id' => $request['customer_id'],
            'transaction_no' => $request['transaction_no'],
            'review_type' => $request['review_type'],
            'rating_number' => $request['rating_number'],
            'comment' => $request['comment'],
                 ] );
        return $result;
    }

    public static function edit_review($request){
        $result= Review::where('id', $request['id'])
                        ->update( [
                            'customer_id' => $request['customer_id'],
                            'transaction_no' => $request['transaction_no'],
                            'review_type' => $request['review_type'],
                            'rating_number' => $request['rating_number'],
                            'comment' => $request['comment'],
                                ] );
        return $result;
    }

    public static function get_review_detail($request){
        $result= Review::select('reviews.*','reservations.be_room_type_nm','reservations.be_rate_plan_name',
                                'reservations.ttl_adult','reservations.ttl_children', 'reservations.ttl_room',
                                'reservations.checkin_dt','reservations.checkout_dt',
                                'guests.full_name as guest_full_name','hotels.name as hotel_name')
                                // ,'msystems.system_value as country')
                ->join('reservations','reservations.transaction_no','=','reviews.transaction_no')
                ->join('guests','guests.id','=','reservations.customer_id')
                // ->join('msystems','msystems.system_cd','=','guests.country')
                ->join('hotels','hotels.id','=','reservations.hotel_id')
                // ->where('msystems.system_type','=','country')
                ->where('reviews.id','=',$request)
                ->first();
        return $result;
    }

    public static function get_review($request){
        $result= Review::select('reviews.*','reservations.be_room_type_nm','reservations.be_rate_plan_name',
                                'reservations.ttl_adult','reservations.ttl_children',
                                'reservations.checkin_dt','reservations.checkout_dt',
                                'guests.full_name as guest_full_name')
                                // ,'msystems.system_value as country')
                ->join('reservations','reservations.transaction_no','=','reviews.transaction_no')
                ->join('guests','guests.id','=','reservations.customer_id')
                // ->join('msystems','msystems.system_cd','=','guests.country')
                // ->where('msystems.system_type','=','country')
                ->where('reviews.review_type','=',1)
                ->where('reservations.hotel_id','=',$request)
                ->orderBy('reviews.rating_number','DESC')
                ->orderBy('reviews.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_review_by_date($request){
        $request['dtFrom'] = date('Y-m-d', strtotime('-1 days', strtotime($request['dtFrom'])));
        $request['dtTo'] = date('Y-m-d', strtotime('+1 days', strtotime($request['dtTo'])));
        $result= Review::select('reviews.*','reservations.be_room_type_nm','reservations.be_rate_plan_name',
                                'reservations.ttl_adult','reservations.ttl_children',
                                'reservations.checkin_dt','reservations.checkout_dt',
                                'guests.full_name as guest_full_name')
                                // ,'msystems.system_value as country')
                ->join('reservations','reservations.transaction_no','=','reviews.transaction_no')
                ->join('guests','guests.id','=','reservations.customer_id')
                // ->join('msystems','msystems.system_cd','=','guests.country')
                // ->where('msystems.system_type','=','country')
                ->where('reviews.review_type','=',1)
                ->where('reservations.hotel_id','=',$request['hotel_id'])
                ->whereBetween('reviews.created_at',[$request['dtFrom'],$request['dtTo']])
                ->orderBy('reviews.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_review_user($request){
        $result= Review::select('reviews.*','reservations.be_room_type_nm','reservations.be_rate_plan_name',
                                'reservations.ttl_adult','reservations.ttl_children', 'reservations.ttl_room',
                                'reservations.checkin_dt','reservations.checkout_dt',
                                'guests.full_name as guest_full_name',
                                // 'msystems.system_value as country',
                                'hotels.name as hotel_name')
                ->join('reservations','reservations.transaction_no','=','reviews.transaction_no')
                ->join('guests','guests.id','=','reservations.customer_id')
                // ->join('msystems','msystems.system_cd','=','guests.country')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                // ->where('msystems.system_type','=','country')
                ->where('reviews.review_type','=',1)
                ->where('guests.id_member','=',$request['user_id'])
                ->orderBy('reviews.created_at','DESC')
                ->get();
        return $result;
    }

    public static function get_review_user_by_date($request){
        $request['startDate'] = date('Y-m-d', strtotime('-1 days', strtotime($request['startDate'])));
        $request['endDate'] = date('Y-m-d', strtotime('+1 days', strtotime($request['endDate'])));
        $result= Review::select('reviews.*','reservations.be_room_type_nm','reservations.be_rate_plan_name',
                                'reservations.ttl_adult','reservations.ttl_children', 'reservations.ttl_room',
                                'reservations.checkin_dt','reservations.checkout_dt',
                                'guests.full_name as guest_full_name',
                                // 'msystems.system_value as country',
                                'hotels.name as hotel_name')
                ->join('reservations','reservations.transaction_no','=','reviews.transaction_no')
                ->join('guests','guests.id','=','reservations.customer_id')
                // ->join('msystems','msystems.system_cd','=','guests.country')
                ->join('hotels', 'hotels.id', '=', 'reservations.hotel_id')
                // ->where('msystems.system_type','=','country')
                ->where('reviews.review_type','=',1)
                ->where('guests.id_member','=',$request['user_id'])
                ->whereBetween('reviews.created_at',[$request['startDate'],$request['endDate']])
                ->orderBy('reviews.created_at','DESC')
                ->get();
        return $result;
    }

}
