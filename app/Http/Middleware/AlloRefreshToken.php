<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Helpers\MPC\Adapter\RefreshToken;

class AlloRefreshToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $response = $next($request);
    $oriRes = json_decode($response->content());

    // return response of controller if error
    if (!$oriRes->status)
      return $response;

    // req. refresh token
    try {

      $refToken = $request->header('refreshToken');
      $reqBody = [
        'requestData' => [
          'refreshToken' => $refToken,
          'grantType' => 'REFRESH_TOKEN',
        ],
        'transactionNo' => self::genTransNo()
      ];

      $helper = new RefreshToken($reqBody);
      $req = $helper->send();
      $res = json_decode($req->getBody()->getContents());

      if (
        !isset($res->responseData) ||
        !isset($res->responseData->accessToken) ||
        !isset($res->responseData->refreshToken)
      ) throw new \Exception("Failed to refresh token");

      $oriRes->data->accessToken = $res->responseData->accessToken;
      $oriRes->data->refreshToken = $res->responseData->refreshToken;
      $response->setContent(json_encode($oriRes));

      return $response;
    } catch (ValidationException $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => $e->status,
        'data' => $e->errors()
      ], $e->status);
    } catch (\Exception $e) {
      return response([
        'status' => false,
        'message' => $e->getMessage(),
        'code' => 500,
        'data' => $oriRes
      ], 500);
    }
  }

  private static function genTransNo()
  {
    $date = date('ymd');
    $middleNo = 'ARKTHG' . mt_rand(100000000000, 999999999999) . mt_rand(10000000, 99999999);;
    $data = $date . $middleNo;
    return $data;
  }
}
