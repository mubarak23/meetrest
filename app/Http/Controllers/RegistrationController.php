<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Meeting;
use JWTAuth;
class RegistrationController extends Controller
{
    public function __construct(){
        //setup middleware here
        $this->middleware('jwt.auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'meeting_id' => 'required',
            'user_id' => 'required'
        ]);

        $meeting_id = $request->input('meeting_id');
        $user_id = $request->input('user_id');
        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $message = [
            'message' => 'User is Already Resgistered for a meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
            ]
        ];
        if($meeting->users()->where('user_id', $user->id)->first()){
            return response()->json($message, 404);
             }

        $user->meetings()->attach($meeting);
        //$meeting->users()->attach($user_id);
        $response = [
            'message' => 'User is Already Resgistered for a meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/' . $meeting->id,
                'method' => 'DELETE'
                ]
            ];

        return response()->json($response, 201);

        //return 'it is working';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        //$meeting->users()->detach();
        if(!$user = JWTAuth::parseToken()->authenticate()){
            return response()->json(["message" => "User Not Found"], 404);
        }
        if(!$meeting->users()->where('users.id', $user->id)->first()) {
            return response()->json([
                'message' => 'User not register for the meeting, Delete Operation is not successful'
            ], 401);
        }

        $meeting->users()->detach($user->id);

        $response = [
            'message' => 'user Unresgistered for a meeting',
            'meeting' => $meeting,
            'user' => $user->id,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/1',
                'method' => 'POST',
                'param' => 'user_id, meeting_id'
            ]
        ];
        return response()->json($response, 200);
    }
}
