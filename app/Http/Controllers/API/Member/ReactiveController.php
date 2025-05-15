<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Members;

class ReactiveController extends Controller
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
      $val = $request->validate([
        'user_id' => 'required|numeric',
      ]);

      $member = Members::withTrashed()
        ->where('id',$val['user_id'])
        ->first();

      if (!$member) 
        throw new \Exception('Member not found', 404);

      $member->restore();

      return response()->json(
        [
          'status' => true,
          'message' => 'Success restore member',
          'code' => 200,
          'data' => $member, 
        ], 
        200
      );

    } catch ( \Exception $e ) {
      return response()->json(
        [
          'status' => false,
          'message' => $e->getMessage(),
          'code' => $e->getCode(),
          'data' => null, 
        ], 
        $e->getCode()
      );
    }
  }
}
