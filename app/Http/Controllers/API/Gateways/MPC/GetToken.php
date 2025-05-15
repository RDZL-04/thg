<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\GetToken as GetTokenHelper;
use Illuminate\Http\Request;

class GetToken extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {
    $helper = new GetTokenHelper($request->all());
    $res = $helper->send();

    return response($res, 200);
  }
}