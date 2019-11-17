<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

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
        $user = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'signin' => [
                'href'=> 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email password'
            ]
        ];
        $response = [
            'message' => 'User Created',
            'user' => $user
        ];

        return  response()->json($response, 201);
    }

    public function signin(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        $email = $request->input('email');
        $password = $request->input('password');
        $user = [
            'name' => 'name',
            'email' => $email,
            'password' => $password,
        ];
        $response = [
            "message" => "User sign in",
            'user' => $user
        ];
        return response()->json($response, 200);
        //return 'it is working';
    }

}
