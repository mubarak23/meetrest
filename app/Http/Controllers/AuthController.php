<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
//header("Access-Control-Allow-Origin: *");
class AuthController extends Controller
{
    //

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $user = new User([
            'name'  => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);
        if($user->save()){
            $user->signin = [
                'href'=> 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email password'
            ];
            $response = [
                'message' => 'User Created',
                'user' => $user
            ];

            return  response()->json($response, 201);
        };

        $response = [
            'message' => 'AN Error occur'
        ];

        return  response()->json($response, 401);
    }

    public function signin(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        //$email = $request->input('email');
        //$password = $request->input('password');
         $credentials = $request->only('email', 'password');   
       try {
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json([
                    "message" => "Invalid Credentials"
                ], 401);
            }

       } catch( JWTException $e){
        return response()->json([
            "message" => "Could not create Token"
        ], 500);
       }
       
       return response()->json([
           "Token" => $token
       ], 200);

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 20160
        ]);
    }
    

}
