<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\AcquirableCoupon as AcquirableCouponHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AcquirableCoupon extends Controller
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

      $currPage = $request->query('page') ?? 1;
      $dataLimit = $request->query('limit') ?? 10;

      $reqBody = [
        "transactionNo" => $this->genTransNo(),
        "requestData" => [
          "accessToken" => $request->header('accessToken'),
          "page" => [
            "currentPage" => $currPage,
            "pageSize" => $dataLimit,
          ],
        ],
      ];
  
      $helper = new AcquirableCouponHelper($reqBody);
      $res = $helper->send();
  
      return response($res, 200);

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