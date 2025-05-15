<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\ConsumePoint as ConsumePointHelper;
use Illuminate\Http\Request;

class ConsumePoint extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {

    $request->validate([
      'amount' => 'required',
    ]);

    $reqBody = [
      'transactionNo' => $this->genTransNo(),
      'requestData' => [
        'amount' => $request->input('amount'),
        'accessToken' => $request->header('accessToken'),
        'externalMerchantId' => '00000002',
        'externalMerchantName' => "Trans Hotel Group - TLH",
        'orderNo' => '',
        'acquirer' => 'BANK_MEGA',
      ]
    ];

    $helper = new ConsumePointHelper($reqBody);
    $res = $helper->send();

    return response($res, 200);
  }
}