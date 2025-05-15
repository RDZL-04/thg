<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\AcquirableCoupon as AcquirableCouponHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AllAcquirableCoupon extends Controller
{
  private string $token;
  private int $page = 1;
  private int $limit = 5;
  private Object $rawRes;
  private bool $goNextPage = false;
  private array $allCoupons = [];

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    try {

      $this->token = $request->header('accessToken');
      $this->reqPerPagination();
      
      return response([
        'status' => true,
        'message' => "List all acquirable coupon",
        'code' => 200,
        'data' => [
          'coupons' => $this->allCoupons
        ]
      ], 200);

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

  private function reqPerPagination()
  {
    $reqBody = [
      "transactionNo" => $this->genTransNo(),
      "requestData" => [
        "accessToken" => $this->token,
        "page" => [
          "currentPage" => $this->page,
          "pageSize" => $this->limit,
        ],
      ],
    ];

    $helper = new AcquirableCouponHelper($reqBody);
    $res = $helper->send();
    $this->rawRes = json_decode($res->getBody()->getContents());

    $this->setDataCoupon();
    $this->setDataPagination();
    $this->doNextPage();
  }

  private function setDataPagination()
  {
    if (!isset($this->rawRes->responseData) || !isset($this->rawRes->responseData->page)) 
      return;

    if ($this->rawRes->responseData->page->totalPage > $this->page) {
      $this->goNextPage = true;
      $this->page++;
      return;
    }

    $this->goNextPage = false;
    return;
  }

  private function setDataCoupon()
  {
    if (!isset($this->rawRes->responseData) || !isset($this->rawRes->responseData->couponList)) 
      return;

    $this->allCoupons = array_merge($this->allCoupons, $this->rawRes->responseData->couponList);
  }

  private function doNextPage()
  {
    if (!$this->goNextPage) return;

    $this->reqPerPagination();
  }
}
