<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\BalancePoint as BalancePointHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BalancePoint extends Controller
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

      $reqBody = [
        "transactionNo" => $this->genTransNo(),
        "requestData" => [
          "accessToken" => $request->header('accessToken')
        ],
      ];

      $helper = new BalancePointHelper($reqBody);
      $res = $helper->send();
      $resData = json_decode($res->getBody()->getContents());

      return response([
        'status' => true,
        'message' => "Success get balance point",
        'code' => 200,
        'data' => $resData
      ],200);

    } catch ( ValidationException $e ) {

      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => $e->status,
        'data' => $e->errors()
      ], $e->status);

    } catch ( \Exception $e ) {

      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => 500,
        'data' => null
      ], 500);

    }
  }
}