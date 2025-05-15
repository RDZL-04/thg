<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\Helpers\MPC\Body as MPCBody;
use App\Http\Controllers\Helpers\MPC\Header as MPCHeader;
use App\Http\Controllers\Helpers\MPC\Request as MPCRequest;

class Controller extends BaseController
{
  protected string $transNo;
  protected MPCBody $mpcBody;
  protected MPCHeader $mpcHeader;
  protected MPCRequest $mpcRequest;

  protected function genTransNo()
  {
    $date = date('ymd');
    $middleNo = 'ARKTHG' . mt_rand(100000000000, 999999999999) . mt_rand(10000000, 99999999);;
    $data = $date . $middleNo;
    return $data;
  }
}
