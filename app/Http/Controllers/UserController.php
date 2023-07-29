<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function UserRegistration(Request $request){
        try{
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);
            return response()->json([
                'message' => 'User Registration Successfully',
                'status' => 'success'
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => 'User Registration Failed',
                'status' => 'failed'
            ]);
        }
    }

    function UserLogin(Request $request){
        $count = User::where('email','=',$request->input('email'))
        ->where('password','=',$request->input('password'))
        ->count();

        if($count===1){
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status'=>'success',
                'message'=>'User Login Success',
                'token'=>$token
            ]);
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'unauthorized'
            ]);
        }

    }

    function SendOTPCode(Request $request){
        $email=$request->input('email');
        $otp = rand(1000, 9999);

        $count = USer::where('email','=',$email)->count();

        if($count == 1){
            Mail::to($email)->send(new OTPMail($otp));

            User::where('email','=',$email)->update(['otp'=>$otp]);

            return response()->json([
                'status'=>'success',
                'message'=>'OTP Send Success'
            ]);
            
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'unauthorized'
            ]);
        }

    }


}
