<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationTmp extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'reservations_tmp';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_key', 'uniqueId', 'hotelCode', 'roomTypeCode', 'roomTypeName', 'ratePlanName', 'start', 'end'
        , 'adult', 'child', 'room', 'is_member', 'price', 'tax', 'ratePlanCode', 'ratePlanType', 'amountAfterTax', 'amountAfterTaxRoom'
        , 'discount', 'discountIndicator', 'discountIndicatorRoom', 'discountIndicatorServ', 'discountRoom', 'discountServ'
        , 'grossAmountBeforeTax', 'grossAmountBeforeTaxRoom', 'grossAmountBeforeTaxServ', 'reservationStatus'
        , 'currency', 'duration', 'device_id', 'promotionId', 'discountCode'
    ];

    public static function save_reservation_tmp($request)
    {
        if(empty($request['device_id'])){
            $request['device_id'] = null;
        }
        // dd($request['be_amountBeforeTaxServ']);
        $result= ReservationTmp::updateOrCreate(
            [
                'api_key' => $request['api_key'], 
                'uniqueId' => $request['uniqueId']],
            [
                'api_key' => $request['api_key'],
                'uniqueId' => $request['uniqueId'],
                'hotelCode' => $request['hotelCode'],
                'roomTypeCode' => $request['roomTypeCode'],
                'roomTypeName' => $request['roomTypeName'],
                'ratePlanName' => $request['ratePlanName'],
                'start' => $request['start'],
                'end' => $request['end'],
                'adult' => $request['adult'],
                'child' => $request['child'],
                'room' => $request['room'],
                'is_member' => $request['is_member'],
                'price' => $request['price'],
                'tax' => $request['tax'],
                'ratePlanCode' => $request['ratePlanCode'],
                'ratePlanType' => $request['ratePlanType'],
                'amountAfterTax' => $request['amountAfterTax'],
                'amountAfterTaxRoom' => $request['amountAfterTaxRoom'],
                'discount' => $request['discount'],
                'discountIndicator' => $request['discountIndicator'],
                'discountIndicatorRoom' => $request['discountIndicatorRoom'],
                'discountIndicatorServ' => $request['discountIndicatorServ'],
                'discountRoom' => $request['discountRoom'],
                'discountServ' => $request['discountServ'],
                'grossAmountBeforeTax' => $request['grossAmountBeforeTax'],
                'grossAmountBeforeTaxRoom' => $request['grossAmountBeforeTaxRoom'],
                'grossAmountBeforeTaxServ' => $request['grossAmountBeforeTaxServ'],
                'reservationStatus' => $request['reservationStatus'],
                'currency' => $request['currency'],
                'duration' => $request['duration'],
                'device_id' => $request['device_id'],
                'promotionId' => $request['promotionId'],
                'discountCode' => $request['discountCode'],
            ] );
        return $result;
    }

    public static function get_reservation_tmp($request){
        $result =ReservationTmp::where('', $request['api_key'])
                ->where('uniqueId', $request['api_key'])
                ->select('reservations.*')
                ->first();
        return $result;
    }
}
