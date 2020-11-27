<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\DetailUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use Mail;
use App\Mail\MailVerification;

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

        $registerLogin = array('email' => $registrationData['email'], 
                                'password' => $registrationData['password'],
                                'role' => $registrationData['role']);

        $registerDetails = array('email' => $registrationData['email'], 
                                    'nama_user' =>$registrationData['nama_user'],
                                    'telepon' => $registrationData['telepon']);

        try {

            $user = User::create($registerLogin);
            $detail =  DetailUser::create($registerDetails);

            $detail = [
                'name' => $registrationData['nama_user'],
                'email' => $user->id
            ];
            Mail::to($registrationData['email'])->send(new MailVerification($detail));
            
            return response([
                'message' => 'Register Success',
                'user' => $user,
                'detail' => $detail
            ], 200);
        }
        catch(Exception $e) {
            return response(['message' => 'Registration Failed!', 'error' => $e], 400);
        }
    }

    public function update(Request $request, $id) {
        $requestData = $request->all();

        $user = User::findOrFail($id);
        $details = DetailUser::findOrFail($requestData['email']);

        if(!is_null($user) && !is_null($details)) {
            $validate = Validator::make($requestData, [
                'email' => 'required',
                'password' => 'required',
                'nama_user' => 'required',
                'telepon' => 'required'
            ]);
            
            if(isset($requestData['nama_user'])){
                $details->nama_user = $requestData['nama_user'];
            }

            if(isset($requestData['telepon'])){
                $details->telepon = $requestData['telepon'];
            }

            if(isset($requestData['oldPassword'])) {
                if(password_verify($requestData['oldPassword'],$user->password)) { 
                    $user->password = bcrypt($requestData['newPassword']);
                }
                else {
                    return response(['message' => 'Old Password does not match'], 400);
                }
            }

            if($user->save() && $details->save()) {
                return response([
                    'message' => 'Update Profile Success',
                    'user' => $user,
                    'details' => $details
                ], 200);
            }
    
            return response([
                'message' => 'Update Profile Failed',
                'data' => null
            ], 400);

        }
        else {
            return response(['message' => 'Data tidak ditemukan'], 400);
        }

        

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
        
        if(is_null($user->email_verified_at))
            return response(['message' => 'Email Belum diverifikasi'], 400);

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

    public function verifyEmail($id) {
        $user = User::findOrFail($id);

        if(!is_null($user)) {
            if(is_null($user->email_verified_at)) {
                $user->email_verified_at =  Carbon::now()->format('Y-m-d H:i:s');

                if($user->save()) {
                    return response()->json([
                        'message' => 'Verifikasi Berhasil',
                        'time' => $user->email_verified_at
                    ], 200);
                }
                else {
                    return response()->json([
                        'message' => 'Verifikasi Gagal'
                    ], 400);
                }
            }
            else {
                return response()->json([
                    'message' => 'Akun Telah diverifikasi'
                ], 400);
            }
        }
        else {
            return response()->json([
                'message' => 'Akun tidak ditemukan'
            ], 400);
        }
        
        return response()->json([
            'message' => 'Verifikasi Gagal'
        ], 400);
    }
}
