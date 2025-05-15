<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditProfileCallback extends Controller
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
            if(!empty($request['updated'])){
                if($request['updated'] == 'true'){
                    $response = [
                        'status' => true,
                        'message' => __('message.data_update_success'),
                        'code' => 200,
                        'data' => $request->all()
                    ];

                } elseif($request['updated'] == 'false'){
                    $response = [
                        'status' => false,
                        'message' => __('message.data_update_failed'),
                        'code' => 200,
                        'data' => $request->all()
                    ];
                }
                return view('mpccallback',['response' =>json_encode($response)]);
                // return response()->json($response, 200);
            }

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

    /*
	 * Function: Log Request
	 * Param: 
	 *	$data	: mixed string/array
	 */
	private function logs($data)
	{
		$file = 'logs_edit_profile_allo.txt';
		
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
		$file = 'logs_edit_profile_allo.txt';
		
		if(file_exists($file)) {
			unlink($file);
		}
		
		// Open the file to get existing content
		file_put_contents($file, '');
		
	}
}
