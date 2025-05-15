<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Adapter\AddPoint as AddPointHelper;
use Illuminate\Http\Request;

class AddPoint extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {
    $helper = new AddPointHelper($request->all());
    $res = $helper->send();

    return response($res, 200);
  }
}