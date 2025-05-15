<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\ReservationTmp;

class UpdateResvTmpController extends Controller
{
  private array $rules = [
    'uniqueId' => 'required|string',
    'hotelCode' => 'required|integer',
    'roomStays.*.roomRates.*.roomTypeCode' => 'nullable',
    'roomStays.*.roomRates.*.roomTypeName' => 'nullable',
    'roomStays.*.ratePlans.*.ratePlanName' => 'nullable',
    'resGlobalInfo.timeSpan.start' => 'nullable',
    'resGlobalInfo.timeSpan.end' => 'nullable',
    'resGlobalInfo.guestCounts.*.ageQualifyingCode' => 'nullable',
    'resGlobalInfo.guestCounts.*.count' => 'nullable',
    'roomStays.*.roomRates.*.numberOfUnits' => 'nullable',
    'roomStays.*.total.amountAfterTax' => 'nullable',
    'roomStays.*.prodTaxes.outTotalInclusiveTaxes.totalTax' => 'nullable',
    'roomStays.*.ratePlans.*.ratePlanCode' => 'nullable',
    'roomStays.*.ratePlans.*.ratePlanType' => 'nullable',
    'roomStays.*.total.amountAfterTax' => 'nullable',
    'roomStays.*.total.amountAfterTaxRoom' => 'nullable',
    'roomStays.*.total.discount' => 'nullable',
    'roomStays.*.total.discountIndicator' => 'nullable',
    'roomStays.*.total.discountIndicatorRoom' => 'nullable',
    'roomStays.*.total.discountIndicatorServ' => 'nullable',
    'roomStays.*.total.discountRoom' => 'nullable',
    'roomStays.*.total.discountServ' => 'nullable',
    'roomStays.*.total.grossAmountBeforeTax' => 'nullable',
    'roomStays.*.total.grossAmountBeforeTaxRoom' => 'nullable',
    'roomStays.*.total.grossAmountBeforeTaxServ' => 'nullable',
    'reservationStatus' => 'nullable',
    'resGlobalInfo.timeSpan.duration' => 'nullable',
    'roomStays.*.discountCode' => 'nullable',
  ];
  private array $resRawVal;
  private array $updateVal;
  private int $adult = 0;
  private int $child = 0;
  private Float $tax = 0.0;
  private string $dicoundCode = '';

  /**
   * Handle the incoming request.
   * 
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    try {

      $this->resRawVal = $request->validate($this->rules);

      $this->calAdultVal();
      $this->calChildVal();
      $this->calTax();
      $this->setDiscound();
      $this->buildUpdateVal();

      ReservationTmp::where('uniqueId', $this->resRawVal['uniqueId'])
        ->update($this->updateVal);

      return response([
        'status' => true,
        'code' => 200,
        'message' => 'Success update tmp hold reservation',
        'data' => null,
      ], 200);

    } catch ( ValidationException $e ) {

      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => $e->status,
        'data' => $e->errors(), 
      ], $e->status);

    } catch ( \Exception $e ) {
      
      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => 500,
        'data' => null, 
      ], 500);

    }
  }

  // resGlobalInfo.guestCounts.*.ageQualifyingCode = 10 > resGlobalInfo.guestCounts.*.count
  private function calAdultVal() : void {

    $guests = $this->resRawVal['resGlobalInfo']['guestCounts'];
    $adultGeust = array_filter($guests, function ($guest) {
      return $guest['ageQualifyingCode'] === '10';
    });

    if (!$adultGeust) 
      return;

    $this->adult = reset($adultGeust)['count'];
  }

  // resGlobalInfo.guestCounts.*.ageQualifyingCode = 8 > resGlobalInfo.guestCounts.*.count
  private function calChildVal() : void {

    $guests = $this->resRawVal['resGlobalInfo']['guestCounts'];
    $childGeust = array_filter($guests, function ($guest) {
      return $guest['ageQualifyingCode'] === '8';
    });

    if (!$childGeust) 
      return;

    $this->child = reset($childGeust)['count'];
  }

  // roomStays.*.prodTaxes.outTotalInclusiveTaxes.totalTax
  private function calTax() : void {

    if (
      !isset($this->resRawVal['roomStays']) ||
      !isset($this->resRawVal['roomStays'][0]) ||
      !isset($this->resRawVal['roomStays'][0]['prodTaxes']) ||
      !isset($this->resRawVal['roomStays'][0]['prodTaxes']['outTotalInclusiveTaxes']) ||
      !isset($this->resRawVal['roomStays'][0]['prodTaxes']['outTotalInclusiveTaxes']['totalTax'])
    ) return;

    $this->tax = $this->resRawVal['roomStays'][0]['prodTaxes']['outTotalInclusiveTaxes']['totalTax'];

  }

  // roomStays.*.discountCode
  private function setDiscound() : void {
    if (
      !isset($this->resRawVal['roomStays']) ||
      !isset($this->resRawVal['roomStays'][0]) ||
      !isset($this->resRawVal['roomStays'][0]['discountCode'])
    ) return ;

    $this->dicoundCode = $this->resRawVal['roomStays'][0]['discountCode'];
  }

  /**
   *  is_member > x
   *  promotionId > x
   *  device_id > x
   */
  private function buildUpdateVal() : void {
    $this->updateVal = [
      'hotelCode'                 => $this->resRawVal['hotelCode'], // hotelCode
      'roomTypeCode'              => $this->resRawVal['roomStays'][0]['roomRates'][0]['roomTypeCode'], // roomStays.*.roomRates.*.roomTypeCode
      'roomTypeName'              => $this->resRawVal['roomStays'][0]['roomRates'][0]['roomTypeName'], // roomStays.*.roomRates.*.roomTypeName
      'ratePlanName'              => $this->resRawVal['roomStays'][0]['ratePlans'][0]['ratePlanName'], // roomStays.*.ratePlans.*.ratePlanName
      'start'                     => $this->resRawVal['resGlobalInfo']['timeSpan']['start'], // resGlobalInfo.timeSpan.start
      'end'                       => $this->resRawVal['resGlobalInfo']['timeSpan']['end'], // resGlobalInfo.timeSpan.end
      'adult'                     => $this->adult,
      'child'                     => $this->child,
      'room'                      => $this->resRawVal['roomStays'][0]['roomRates'][0]['numberOfUnits'], // roomStays.*.roomRates.*.numberOfUnits
      'price'                     => $this->resRawVal['roomStays'][0]['total']['amountAfterTax'], // roomStays.*.total.amountAfterTax
      'tax'                       => $this->tax,
      'ratePlanCode'              => $this->resRawVal['roomStays'][0]['ratePlans'][0]['ratePlanCode'], //roomStays.*.ratePlans.*.ratePlanCode
      'ratePlanType'              => $this->resRawVal['roomStays'][0]['ratePlans'][0]['ratePlanType'], // roomStays.*.ratePlans.*.ratePlanType
      'amountAfterTax'            => $this->resRawVal['roomStays'][0]['total']['amountAfterTax'], // roomStays.*.total.amountAfterTax
      'amountAfterTaxRoom'        => $this->resRawVal['roomStays'][0]['total']['amountAfterTaxRoom'], // roomStays.*.total.amountAfterTaxRoom
      'discount'                  => $this->resRawVal['roomStays'][0]['total']['discount'], // roomStays.*.total.discount
      'discountIndicator'         => $this->resRawVal['roomStays'][0]['total']['discountIndicator'], // roomStays.*.total.discountIndicator
      'discountIndicatorRoom'     => $this->resRawVal['roomStays'][0]['total']['discountIndicatorRoom'], // roomStays.*.total.discountIndicatorRoom
      'discountIndicatorServ'     => $this->resRawVal['roomStays'][0]['total']['discountIndicatorServ'], // roomStays.*.total.discountIndicatorServ
      'discountRoom'              => $this->resRawVal['roomStays'][0]['total']['discountRoom'], // roomStays.*.total.discountRoom
      'discountServ'              => $this->resRawVal['roomStays'][0]['total']['discountServ'], // roomStays.*.total.discountServ
      'grossAmountBeforeTax'      => $this->resRawVal['roomStays'][0]['total']['grossAmountBeforeTax'], // roomStays.*.total.grossAmountBeforeTax
      'grossAmountBeforeTaxRoom'  => $this->resRawVal['roomStays'][0]['total']['grossAmountBeforeTaxRoom'], // roomStays.*.total.grossAmountBeforeTaxRoom
      'grossAmountBeforeTaxServ'  => $this->resRawVal['roomStays'][0]['total']['grossAmountBeforeTaxServ'], // roomStays.*.total.grossAmountBeforeTaxServ
      'reservationStatus'         => $this->resRawVal['reservationStatus'], // reservationStatus
      'duration'                  => $this->resRawVal['resGlobalInfo']['timeSpan']['duration'], // resGlobalInfo.timeSpan.duration
      'discountCode'              => $this->dicoundCode,
    ];
  }
}
