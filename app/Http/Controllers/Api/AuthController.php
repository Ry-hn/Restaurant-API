<?php

namespace App\Http\Controllers\Api;

use File;
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
            'email' => 'required|unique:users',
            'password' => 'required',
            'nama_user' => 'required',
            'telepon' => 'required|unique:users'
        ]);

        if($validate->fails()) 
            return response(['message' => $validate->errors()], 400);
        
        $registrationData['password'] = bcrypt($request->password);
        
        try {

            $user = User::create($registrationData);
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

        $user = User::find($id);  
    
        if(is_null($user))
            return response(['message' => 'user not found'], 404);
        
        if(isset($requestData['nama_user']))
            $user->nama_user = $requestData['nama_user'];
        
        if(isset($requestData['telepon']))
            $user->telepon = $requestData['telepon'];

        // email bisa diubah atau tidak ??
        // if(isset($requestData['email']))
        //     $user->email = $requestData['email'];

        /* cek dulu apakah password yang dimasukin ada di db nda
             kalau ada baru bisa ganti */
        if(isset($requestData['oldPassword'])) {
            if(password_verify($requestData['oldPassword'],$user->password)) 
                $user->password = bcrypt($requestData['newPassword']);
            else 
                return response(['message' => 'Old Password does not match'], 420);
        }

        if($user->save()) {
            return response([
                'message' => 'Update Profile Success',
                'user' => $user
            ], 200);
        }

        return response([
            'message' => 'Update Profile Failed',
            'user' => null
        ], 400);

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
            return response(['message' => 'Email Belum diverifikasi'], 421);

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
        ], 200);
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

    public function destroy($id) {
        $user = User::findOrFail($id);

        if(is_null($user)) {
            return response([
                'message' => 'User Not Found',
                'user' => null
            ], 404);
        }

        if($user->delete()) {
            return response([
                'message' => 'Delete User Success',
                'user' => $user
            ], 200);
        }

        return response([
            'message' => 'Delete User Failed',
            'user' => null
        ], 400);
    }

    public function uploadImage(Request $request) {

        $reqData = $request->all();

        $myData = User::findOrFail($reqData['id']);

        if($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = $reqData['id'] . '.' . $img->getClientOriginalExtension();
            $request->file('gambar')->move(public_path('/Photo/Profile'), $filename);
            $path = public_path($filename);

            $myData->gambar = $filename;
        }
        else {
            return response(['message' => 'No gambar'], 400);
        }

        if($myData->save()) {
            return response([
                'message' => 'Update Image Success',
                'user' => $myData
            ], 200);
        }
    }
    
    public function getImage($location) {

        $path = public_path() . '/Photo/Profile/' . $location;

        if(File::exists($path))
            return response()->file($path);

        return response(['message' => 'file not found', 'path' => $path], 404);
    }
}
