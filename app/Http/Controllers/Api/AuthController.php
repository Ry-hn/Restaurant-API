<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\DetailUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller {

    public function register(Request $request) {
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'email' => 'required|unique:users|unique:detail_users',
            'password' => 'required',
            'nama_user' => 'required',
            'telepon' => 'required|unique:detail_users'
        ]);

        if($validate->fails()) 
            return response(['message' => $validate->errors()], 400);
        
        $registrationData['password'] = bcrypt($request->password);

        $registerLogin = array('email' => $registrationData['email'], 'password' => $registrationData['password']);
        $registerDetails = array('email' => $registrationData['email'], 
                                    'nama_user' =>$registrationData['nama_user'],
                                    'telepon' => $registrationData['telepon']);


        $user = User::create($registerLogin);
        $detail =  DetailUser::create($registerDetails);

        return response([
            'message' => 'Register Success',
            'user' => $user,
            'detail' => $detail
        ], 200);
    }

    public function login(Request $request) {
        $loginData = $request->all();
        
        $validate = Validator::make($loginData, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails()) 
            return response(['message' => $validate->errors()], 400);
        
        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'], 401);

        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;
    
        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
