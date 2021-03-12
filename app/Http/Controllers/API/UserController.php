<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\GeneralServices;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class UserController extends Controller
{
	use GeneralServices;

    public function login(Request $request)
	{
		$role = [
			'email' => 'Required',
			'password' => 'Required',
		];

		$validateData = $this->ValidateRequest($request->all(), $role);

		if (!empty($validateData)) {
			return $validateData;
		}
		// Find the member by email
		$cek_member = UserModel::select('*')->where('email','=',$request['email'])->first();
		if (empty($cek_member)) {
            return $this->ResponseJson(406,"Failed! Email Address Not Found!",array());
		}
		$cek_member->makeVisible('password');
		if (Hash::check($request['password'], $cek_member->password)) {
			if ($cek_member->status=="active") {
                $cek_member['User-Token'] = $this->generateJwt($cek_member);
                return $this->ResponseJson(200,"Success Login!",$cek_member->makeHidden(['password','token']));
			}else{
                return $this->ResponseJson(406,"Failed! Member inactive!",array());
			}
		}
        return $this->ResponseJson(406,"Failed! invalid password",array());
	}
    public function register(Request $request)
    {
        $rules = [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users,email',
			'password' => [
                                'required',
                                'string',
                                'confirmed',
                                'min:8',             // must be at least 8 characters in length
                                'regex:/[a-z]/',      // must contain at least one lowercase letter
                                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                                'regex:/[0-9]/',      // must contain at least one digit
                                'regex:/[@$!%*#?&]/', // must contain a special character
                            ],
            'password_confirmation ' => 'string',
            'gender' => 'nullable',
            'photo' => 'nullable'
        ];
        $validateData = $this->ValidateRequest($request->all(), $rules);

        if (!empty($validateData)) {
			return $validateData;
		}
        $request['status'] = "active";
        $request['password'] = hash::make($request['password']);

        $save = UserModel::create($request->except(['_method','_token','password_confirmation']));
        if(!$save){
            return $this->ResponseJson(500,"Failed! Server Error!",array());
        } 
        return $this->ResponseJson(200,"User succesfully created",array());
    
    }
    public function logout(Request $request){
        $check  = $this->getDataFromToken($request);
        if(!$check){
            return $this->ResponseJson(406,"Failed! Invalid User Token!",array());
        }
        
        $save = $this->updateToken($check,'');

        return $this->ResponseJson(200,"User succesfully Logout",array());
    }
    public function checkToken(Request $request){
        $check  = $this->getDataFromToken($request);
        if(!$check){
            return $this->ResponseJson(406,"Failed! Invalid UserToken!",array());
        }
        return $this->ResponseJson(200,"User Token Available",array());
    }
}
