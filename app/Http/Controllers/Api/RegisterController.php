<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController as BaseController;
use Validator;
use Carbon\Carbon;

use App\User;
use App\UsersInfo;

class RegisterController extends BaseController
{
	/**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'first_name'=> 'required',
            'last_name'=> 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('ShareBuyUser')->accessToken;
        $success['name'] =  $user->name;

        $userId=$user->id;
        $input['user_id']=$userId;
        $userInfo= UsersInfo::create($input);


        return $this->sendResponse($success, 'User register successfully.');
    }

    public function test(Request $request)
    {   
        $success['hello']=Carbon::now()->timestamp;

        return $this->sendResponse($success, 'Hello');
    }
}