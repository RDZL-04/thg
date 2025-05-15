<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class GetSessionController extends Controller
{
    // Construct
	public function __construct() {
        // set API URL dari APP_ENV
        if(env('APP_ENV') == 'local'){
           $this->url_redirect = env('APP_REDIRECT_URL');
       } elseif(env('APP_ENV') == 'dev') {
           $this->url_redirect = env('APP_REDIRECT_URL_DEV');
       } elseif(env('APP_ENV') == 'prod') {
           $this->url_redirect = env('APP_REDIRECT_URL_PROD');
       }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
	  try
	  {
        $randomString = session()->getId();
        Cookie::queue('authSession', $randomString, 10);
        $response = [
            'status' => true,
            'message' => '',
            'code' => 200,
            'data' => $randomString
        ];
        //   return session()->getId();
          if(!empty($request['osType']))
          {
            //   return $response;
              return view('mpccallback',['response' =>json_encode($response)]);
          } else {
              return redirect()->away($this->url_redirect.'login_register?redirectPageType='.$request['redirectPageType'].'&sessionId='.$randomString."&deviceId=".$request['deviceId']);
          }
        //   return response()->json($response, 200);
      }
      catch(\Exception $e) 
	  {
		  $data = array(
				"status" => false,
				"message" => $e->getMessage()
			);
			
			$this->logs($data);
			
		  return response()->json($data);
	  }
    }
}
