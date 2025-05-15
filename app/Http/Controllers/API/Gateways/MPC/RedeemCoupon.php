<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\RedeemCoupon as RedeemCouponHelper;
use Illuminate\Http\Request;

class RedeemCoupon extends Controller
{

  private $resDummy = [
    "code" => "0",
    "message" => "Success",
    "bizSeqNo" => "123123",
    "transactionNo" => "123123",
    "responseData" => [
      "consumptionNo" => "123123",
      "deductAmt" => ""
    ]
  ];

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {

    if (env('DUMMY_REDEEM_COUPON', false)) {
      return response([
        'status' => true,
        'message' => "Redeem coupon success",
        'code' => 200,
        'data' => $this->resDummy
      ], 200);
    }

    // $request->validate([
    //   'couponNo' => 'required',
    // ]);

    $reqBody = [
      "transactionNo" => $this->genTransNo(),
      "requestData" => [
        "accessToken" => $request->header('accessToken'),
        "couponNo" => $request->input('couponNo'),
        "orderNo" => "473432911",
        "merchantId" => "00000002",
        "merchantName" => "Trans Hotel Group - TLH",
        "transactionTime" => "1690167051812",
        "acquirer" => "BANK_MEGA",
        "orderAmount" => "1230000",
      ],
    ];

    $reqBodyFail = $request->all();

    $helper = new RedeemCouponHelper($reqBodyFail);
    $helper->debug = true;
    $res = $helper->send();

    return response($res, 200);
  }
}