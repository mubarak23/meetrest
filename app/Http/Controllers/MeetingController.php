<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;
use carbon\carbon;
use JWTAuth;

class MeetingController extends Controller
{
    public function __construct(){
        //setup middleware here
        $this->middleware('jwt.auth', ['only' => [
            'update', 'store', 'destroy'
        ]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try{

            $meeting = Meeting::all();
        foreach($meetings as $meeting){
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET'
            ];
        };
        $response = [
            'message' => 'Meeting created successfully',
            'meeting' => $meetings
        ];
        return response()->json($response, 200);

        }
        catch(ModelNotFoundException $ex){
            \abort(422, 'Invaid Request');
        }
        catch(Exception $e){
            \abort(500, 'Could not Fetch Meeting details');
        }
        
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
      //  return Request::header('Authorization');

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:d/m/Y'
        ]);

        if(!$user = JWTAuth::parseToken()->authenticate()){
            return response()->json(['message' => 'User Not Found'], 404);
        }

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

        $meeting = new Meeting([
            'title' => $title,
            'description' => $description,
            'time' => Carbon::createfromformat('d/m/Y', $time)
        ]);
        if($meeting->save()){
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/' . $meeting->id,
                'method' => 'GET'
            ];
            $response = [
                'message' => 'Meeting created successfully',
                'meeting' => $meeting
            ];
            return response()->json($response, 200);

        };


        $response = [
            'message' => 'Meeting created successfully',
        ];
        return response()->json($response, 401);

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
        try{
            $meeting = Meeting::with('users')->where('id', $id)->firstOrFail();
        $meeting->view_meeting = [
            'href' => 'api/v1/meeting',
            'meeting' => 'GET'
        ];
        $response = [
            'message' => 'meeting information',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);
        }catch(ModelNotFoundException $ex){
            \abort(402, 'Invalid Meeting Id Provided');
        }catch(Exception $e){  
            \abort(500, 'Could not Fetch Meeting infor');
        }
        

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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required|date_format:d/m/Y'
        ]);
        if(!$user = JWTAuth::parseToken()->authenticate()){
            return response()->json(["message" => "User Not Found"], 404);
        }
        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $user->id;

        $meeting = Meeting::with('users')->findOrFail($id);
        if(!$meeting->users()->where('users.id', $user_id)->first()) {
            return response()->json([
                'message' => 'User not register for the meeting, update'
            ], 401);
        }
        $meeting->time = Carbon::createfromformat('d/m/Y', $time);
        $meeting->description = $description;
        $meeting->title = $title;
        if(!$meeting->update()){
            return response()->json([
                'message' => 'Error during update'
            ], 402);
        }
        $meeting->view_meeting = [
            'href' => 'api/v1/meeting/' . $meeting->id,
            'meeting' => 'GET'
        ];
        $response = [
            'message' => 'meeting information',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);

        //return 'Update method is working perfectly';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $meeting = Meeting::findOrFail($id);
        if(!$user = JWTAuth::parseToken()->authenticate()){
            return response()->json(["message" => "User Not Found"], 404);
        }
        if(!$meeting->users()->where('uses.id', $user->id)->first()) {
            return response()->json([
                'message' => 'User not register for the meeting, update'
            ], 401);
        }
        $users = $meeting->users;
        $meeting->users()->detach();
        if(!$meeting->delete()){
            foreach ($users as $user){
                $meeting->users()->attach($user);
            }
            return response()->json([
                'mesaage' => 'Deletion Faild'
            ], 404);
        }
        $response = [
            'message' => 'Meeting deleted',
            'create' => [
                'href' =>'aip/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
            ];
            return response()->json($response, 200);

    }
}
