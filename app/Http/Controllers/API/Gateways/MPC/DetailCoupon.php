<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\DetailCoupon as DetailCouponHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DetailCoupon extends Controller
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
        'couponNo' => 'required',
      ]);

      $reqBody = [
        "transactionNo" => $this->genTransNo(),
        "requestData" => [
          "accessToken" => $request->header('accessToken'),
          "couponId" => $request->input('couponId'),
          "couponNo" => $request->input('couponNo'),
        ],
      ];

      $helper = new DetailCouponHelper($reqBody);
      $res = $helper->send();
      $resData = json_decode($res->getBody()->getContents());

      if ($resData->code !== "0")
        throw new \Exception($resData->message);
        
      if (!isset($resData->responseData) || !isset($resData->responseData->couponInstance)) 
        throw new \Exception("Failed to get detail coupon, information data coupon is missing.");

      return response([
        'status' => true,
        'message' => "Success get detail coupon",
        'code' => 200,
        'data' => [
          'coupon' => $resData->responseData->couponInstance
        ]
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
