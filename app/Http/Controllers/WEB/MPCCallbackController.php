<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AlloApiController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request as FacadesRequest;
use App\Http\Controllers\Helpers\Custom\GAfour;

/*
|--------------------------------------------------------------------------
| MPC Callback Controller
|--------------------------------------------------------------------------
|
| Catch Request from MPC API after user do Login or Register
| 
| @author: rangga.muharam@arkamaya.co.id 
| @update: 09 Maret 2021
*/

class MPCCallbackController extends Controller
{
	private string $url_redirect;
	private AlloApiController $AlloApiController;

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
        $this->AlloApiController = new AlloApiController;
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
			$redis = Redis::connection();
			$value = FacadesRequest::cookie('authSession');
			$response = $redis->get($value);
			$authSession = json_decode($response);
			if( empty($authSession) || empty($request['code'])) 
				return response()->json([
					'status' => false,
					'message' => 'Please Re-login',
					'code' => 205,
					'data' => $request->all()
				], 200);
			
			// Construct Mandatory Request
			$request_auth_token = new Request();
			$request_auth_token->merge([
				'code' => $request['code'],
				'codeVerifier' => $authSession->verifier
			]);

			// Construct For Web
			if(!empty($authSession->equipmentId)){ 
				$request_auth_token->merge([
					'equipmentId' => $authSession->equipmentId,
					'deviceId' => $authSession->deviceId
				]);
			} elseif (!empty($authSession->osType)) {
				// Construct For Mobile
				$request_auth_token->merge([
					'osType' => $authSession->osType,
					'deviceId' => $authSession->deviceId
				]);
			}
			// dd($request_auth_token->all());
			$response = $this->AlloApiController->allo_auth_token($request_auth_token);
			$res = (array)$response->getData();

			if( !$res['status'] )
				return response()->json([
					'status' => false,
					'message' => "Please Re-login",
					'code' => 205,
					'data' => $request->all()
				], 200);

			$res['data'] = (array)$res['data'];
			if(!empty($authSession->equipmentId)){ 
				$res['data']['equipmentId'] = $authSession->equipmentId;
			}

			if(!empty((array)$res['data']['responseAllo'])){
				$res['data']['responseAllo'] = (array)$res['data']['responseAllo'];
			} else {
				$res['data']['responseAllo'] = [];
			}

			$response = [
				'status' => true,
				'message' => $res['message'],
				'code' => 200,
				'data' => $res['data']
			];

			if(!empty($authSession->osType)){
				// MOBILE
				Redis::del($value); // Flush Del key sesuai sessionId
				return view('mpccallback',['response' =>json_encode([
					'status' => true,
					'message' => $response['message'],
					'code' => 200,
					'data' => $res['data']
				])]);
			} else {
				// start send to GA4
				$ga4Helper = new GAfour();
				$ga4Helper->clientId = $authSession->equipmentId;
				$ga4Helper->event = strtolower($authSession->redirectPageType);
				$ga4Helper->params = [
					'screenName' => strtolower($authSession->redirectPageType), 
					'screenClass' => strtolower($authSession->redirectPageType) === 'login' ? '/cas-web/#/pages/fio/login/login' : '/cas-web/#/pages/fio/login/register', 
					'mdcId' => $res['data']['mdcid'], 
				];
				$ga4Helper->send();
				// end send to GA4

				//WEB 
				Redis::del($value); // Flush Del key sesuai sessionId
				// dd($this->url_redirect.'mpccallback?response='.urlencode($this->AlloApiController->encryptData(json_encode($response))));
				return redirect()->away($this->url_redirect.'mpccallback?response='.urlencode($this->AlloApiController->encryptData(json_encode($response))));
			}
		} catch(\Exception $e)  {
		  	$data = array(
				"status" => false,
				"message" => $e->getMessage()
			);
			$this->logs($data);
		  	return response()->json($data);
	  	}
    }

    /*
	 * Function: Log Request
	 * Param: 
	 *	$data	: mixed string/array
	 */
	private function logs($data)
	{
		$file = 'logs_allo.txt';
		
		// Open the file to get existing content
		if(file_exists($file)) {
			$current = file_get_contents($file);
		}else{
			$current = '';
		}
				
		// Append a new person to the file
		if(is_array($data)) {
			$current .= json_encode($data);
		}else{
			$current .= $data;
		}
		
		// Write the contents back to the file
		file_put_contents($file, $current);
		//Storage::put($file, $current);
		
	}
	
	/*
	 * Function: Clear Log Request
	 * Param: 
	 *	void
	 */
	private function clear_logs()
	{
		$file = 'logs_allo.txt';
		
		if(file_exists($file)) {
			unlink($file);
		}
		
		// Open the file to get existing content
		file_put_contents($file, '');
		
	}
}
