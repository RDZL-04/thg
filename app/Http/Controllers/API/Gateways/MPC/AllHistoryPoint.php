<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\HistoryPoint as HistoryPointHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AllHistoryPoint extends Controller
{
  private string $token;
  private int $page = 1;
  private int $limit = 5;
  private Object $rawRes;
  private bool $goNextPage = false;
  private array $allHistory = [];

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
        'message' => "List all point history",
        'code' => 200,
        'data' => [
          "histories" => $this->allHistory
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

    $helper = new HistoryPointHelper($reqBody);
    $res = $helper->send();
    $this->rawRes = json_decode($res->getBody()->getContents());

    $this->setDataHistory();
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

  private function setDataHistory()
  {
    if (!isset($this->rawRes->responseData) || !isset($this->rawRes->responseData->pointRecordList)) 
      return;

    $this->allHistory = array_merge($this->allHistory, $this->rawRes->responseData->pointRecordList);
  }

  private function doNextPage()
  {
    if (!$this->goNextPage) return;

    $this->reqPerPagination();
  }
}