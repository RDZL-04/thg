<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\AcquireCoupon as AcquireCouponHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AcquireCoupon extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {
    try {

      $request->validate([
        'couponId' => 'required',
      ]);
  
      $reqBody = [
        "transactionNo" => $this->genTransNo(),
        "requestData" => [
          "accessToken" => $request->header('accessToken'),
          "couponId" => $request->input('couponId'),
        ],
      ];
  
      $helper = new AcquireCouponHelper($reqBody);
      $res = $helper->send();
  
      return response([
        'status' => true,
        'message' => "Success acquire coupon",
        'code' => 200,
        'data' => $res
      ], 200);

    } catch (ValidationException $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => $e->status,
        'data' => $e->errors()
      ], $e->status);
    } catch (\Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => 500,
        'data' => null
      ], 500);
    }
  }
}