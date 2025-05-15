<?php

namespace App\Http\Controllers\API\Gateways\MPC;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\EncryptHelper;
use Illuminate\Http\Request;

class GenCode extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    //
    $EncryptHelper = new EncryptHelper;
    $verifier = $EncryptHelper->create_verifier();
    $codeChallenge = $EncryptHelper->hashingVerifier($verifier);
    return response()->json([
      'verifier' => $verifier,
      'challenge' => $codeChallenge,
    ], 200);
  }
}
