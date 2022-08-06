<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Validator;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|regex:/^[a-zA-Z0-9\s]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            //return failed response
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 400);
        } else {
            try {
                $user = new User;
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = app('hash')->make($request->input('password'));
                $user->save();

                //return successful response
                return response()->json([
                    'status' => 'success',
                    'message' => 'User Created !',
                    'data' => $user,
                ], 201);
            } catch (\Exception $e) {
                //return error message
                return response()->json([
                    'status' => 'failed',
                    'message' => $e,
                ], 409);
            }
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            //return failed response
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = User::where('email', '=', $request->input('email'))->first();
        $user->{"token"} = $this->respondWithToken($token);

        return response()->json([
            'status' => 'success',
            'message' => 'User Logged In !',
            'data' => $user,
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'User Profile Requested !',
            'data' => Auth::user(),
        ], 200);
    }
    //  ===================================================================================
    //  ======================================= MESSAGES ===================================
    //  ===================================================================================

    /**
     * Get message.
     *
     * @return Response
     */
    public function messages(Request $request)
    {
        $messages = Message::all();

        foreach($messages as $ms){
            if($ms->isSenderAnonymous == false){
                $ms->sender = User::find($ms->user_id)->name;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message created successfully!',
            'data' => $messages,
        ], 200);
    }


    /**
     * Post message.
     *
     * @return Response
     */
    public function send_messages(Request $request)
    {
        //validate incoming request
        $validator = Validator::make($request->all(), [
            'receiver' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'message' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'isSenderAnonymous' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            //return failed response
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 400);
        }

        $message = new Message();
        $message->user_id = Auth::user()->id;
        $message->receiver = $request->receiver;
        $message->message = $request->message;
        $message->isSenderAnonymous = $request->isSenderAnonymous;
        $message->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Message created successfully!',
            'data' => $message,
        ], 200);
    }

    //  ===================================================================================
    //  ======================================= LOGOUT ===================================
    //  ===================================================================================

    /**
     * Logout.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'User Logout!',
        ], 200);
    }
}
