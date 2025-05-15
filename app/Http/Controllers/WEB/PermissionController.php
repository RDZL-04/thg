<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use App\Http\Controllers\LOG\ErorrLogController;
use App\Models\RoleAuth;
use Auth;
use DB;

/*
|--------------------------------------------------------------------------
| Permission WEB Controller
|--------------------------------------------------------------------------
|
| Validate,.
| This controller will process Role Permission access
| 
| @author: arif@arkamaya.co.id 
| @update: 02 June 2021 9:47am
*/

class PermissionController extends Controller
{
    public function __construct() {
        // Set header for API Request
        $this->headers = [
            'x-api-key' => env('API_KEY'),
          ];
        $this->LogController = new ErorrLogController;
      }

    /**
     * Function: route and send data to index
     * body: 
     *	$request	: 
    */
      public function index()
      {
        try{

            $name = session('full_name');
            $role = session('role');
            // if(strtolower($role) == 'admin' || Auth::user()->permission('utility-permission-list'))
            if(Auth::user()->permission('utility'))
			{
                $url = url('api').'/user/get_role';
				$client = new Client(); //GuzzleHttp\Client
					// $response = $client->request('GET', 'http://thg-web/api/hotel/get_hotel_all',[
					$response = $client->request('GET', $url,[
						'verify' => false,
						'headers'       => $this->headers,
						'/delay/5',
						 ['connect_timeout' => 3.14]
					]);
				$body = $response->getBody();
				$response = json_decode($body, true);
				$data=$response['data'];
            }
            else
			{
                abort(401);
            }
			
            return view('permission.list',[
				'data' =>$data]
				);
            
          }
          catch (Throwable $e) 
		  {
            return view('permission.list',[
				'data' =>null]
				);
        }
      }
    //
    /**
     * Function: get permission access list for specific role
     * body: 
     *	$request	: role_id
    */
    public function get(Request $request)
    {
        try
		{
			// if(session('role') != 'Admin' && !Auth::user()->permission('utility-permission-list')) {
			if(!Auth::user()->permission('utility')){
				return response()->json(['message' => 'Access denied'], 200);
			}
			
			if($request->role_id == '') {
				return response()->json([], 200);
			}
			
			$sql = "
				SELECT 
					permission_name,
					description,
					IF((
						SELECT count(role_id) 
						FROM role_auth
						WHERE role_id = '{$request->role_id}' AND permission_name = t.permission_name
					)>0,1, 0) AS grant_access
				FROM role_permission t
				ORDER BY t.permission_name
			";
			
            $data = DB::select($sql);
							
			return response()->json($data, 200);
          }
          catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'get_data',
                'actions' => 'get data permission',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
            $response = [
                'status' => false,
                'message' => __('message.internal_erorr'),
                'code' => 500,
                'data' => null, 
            ];
            return response()->json($response, 500);
        }
    }
	
	/**
     * Function: send data user to  table index
     * body: 
     *	$request	: 
    */
    public function  set(Request $request)
    {
        try{
			// if(session('role') != 'Admin' && !Auth::user()->permission('utility-permission-create')) {
			if(!Auth::user()->permission('utility')){
				return response()->json(['message' => 'Access denied'], 200);
			}
			
			$role = $request->role;
			$permission = $request->permission;
			$grant = $request->grant;
			
			if($grant == 'true')
			{
				$data = array(
					'role_id' => $role,
					'permission_name' => $permission
				);
				
				RoleAuth::insert($data);
				
				$response = array('message' => 'Set permission success');
			}
			else
			{
				RoleAuth::where('role_id', $role)->where('permission_name', $permission)->delete();
				
				$response = array('message' => 'Unset Permission success');
			}
			
			return response()->json($response, 200);
			
          }
          catch (Throwable $e) {
            report($e);
            $error = ['modul' => 'set_data',
                'actions' => 'set data permission',
                'error_log' => $e,
                'device' => "0" ];
                $report = $this->LogController->error_log($error);
				
            $response = [
                'status' => false,
                'message' => $response['message'],
                'code' => 500,
                'data' => null, 
            ];
			
            return response()->json($response, 500);
        }
    }

}
