<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Http\Controllers\Helpers\Member\HangingTrans;

class HangingTransController extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Members $member)
  {
    $hangingTrans = new HangingTrans($member);

    return response()->json([
        'status' => true,
        'code' => 200,
        'message' => __('message.data_found' ),
        'data' => $hangingTrans->isHas(),
      ], 200
    );
  }
}
