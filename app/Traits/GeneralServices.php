<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use App\Models\UserModel;
use Firebase\JWT\JWT;

trait GeneralServices {

    protected function generateJwt($user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + (60*60*24)*7,// Expiration time a week
            'user' => $user->email,

        ];
        $token =  JWT::encode($payload, env('JWT_SECRET'));

        $updateTableUser = $this->updateToken($user,$token);
        
        return $token;
        
    }
    public function updateToken($user,$token){
        $updateData['token'] = $token;
        return UserModel::where('id',$user->id)->update($updateData);
    }
    public function getDataFromToken(Request  $request){
        return UserModel::where('token',$request->header('User-Token'))->first();
    }
    public function ResponseJson($status,$message,$data = null){
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data 
        ];
		if($status != 200){
			$response = [
				'status' => false,
				'message' => $message
			];
		}
		return response()->json($response, $status);
	}
    function ValidateRequest($params,$rules){

		$validator = Validator::make($params, $rules);

		if ($validator->fails()) {
			$response = [
				'status' => false,
				// 'message' => $validator->messages()
				'message' =>  $validator->errors()->first()
			];
			return response()->json($response, 406);
		}
	}   
}