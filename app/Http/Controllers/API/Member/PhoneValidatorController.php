<?php

namespace App\Http\Controllers\API\Member;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Members;

class PhoneValidatorController extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Members $member, Request $request)
  {
    try {

      $validated = $request->validate([
        'phone' => 'required|numeric',
      ]);

      return response()->json(
        [
          'status' => true,
          'message' => 'Data found',
          'message_code' => $member->phone === $validated['phone'] ? 'phone_valid' : 'phone_invalid',
          'code' => 200,
          'data' => $member->phone === $validated['phone'], 
        ], 
        200
      );

    } catch ( ValidationException $e ) {
      return response()->json(
        [
          'status' => false,
          'message' => $e->getMessage(),
          'code' => $e->status,
          'data' => $e->errors(), 
        ], 
        $e->status
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
