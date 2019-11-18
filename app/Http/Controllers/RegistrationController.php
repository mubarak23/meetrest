<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use APP\Meeting;
use App\User;

class RegistrationController extends Controller
{
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

        $meeting_id = $request->intput('meeting_id');
        $user_id = $request->input('user_id');
        $meeting = Meeting::findOrFail($meeting_id);
        $user = User::findOrFail($user_id);

        $meeting = [
            'title' => 'title',
            'description' => 'description',
            'time' => 'time',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];
        $user = [
            'name' => 'name'
        ];

        $message = [
            'message' => 'user resgistered for a meeting',
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
        //
        //return 'it is working';
        $meeting = [
            'title' => 'title',
            'description' => 'description',
            'time' => 'time',
            'view_meeting' => [
                'href' => 'api/v1/meeting/1',
                'method' => 'GET'
            ]
        ];
        $user = [
            'name' => 'name'
        ];

        $response = [
            'message' => 'user resgistered for a meeting',
            'meeting' => $meeting,
            'user' => $user,
            'unregister' => [
                'href' => 'api/v1/meeting/registration/1',
                'method' => 'POST',
                'param' => 'user_id, meeting_id'
            ]
        ];
        return response()->json($response, 201);
    }
}
