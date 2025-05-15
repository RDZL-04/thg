<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Helpers\MPC\Body as MPCBody;
use App\Http\Controllers\Helpers\MPC\Header as MPCHeader;
use Illuminate\Http\Request;

class GenHeader extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    $this->mpcBody = new MPCBody($request->all());
    $this->mpcHeader = new MPCHeader($this->mpcBody);
    return response($this->mpcHeader->get(), 200);
  }
}
