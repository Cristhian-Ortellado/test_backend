<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        //validate fields
        $username = $request->input('username');
        $password = $request->input('password');

        //use this way because we don't have a format defined (I used to use form requests)
        if (is_null($username) || is_null($password)) {
            return response()->json([
                "meta" => [
                    "success" => false,
                    "errors" => ["Username and password are required"]
                ]
            ], 400);
        }

        //try to login
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = User::where('username', $username)->first();

            //update last login
            $user->last_login = now();
            $user->save();

            return response()->json([
                'meta' => [
                    'success' => true,
                    'errors' => []
                ],
                'data' => [
                    'token' => $user->createToken("Bearer", ['*'], now()->minutes(config('app.TOKEN_EXPIRATION_TIME_MIN')))->plainTextToken,
                    'minutes_to_expire' => config('app.TOKEN_EXPIRATION_TIME_MIN')
                ]
            ]);
        };

        //return Invalid Credentials instead of the username of the user because client never should know if the user exists or not
        return response()->json([
            "meta" => [
                'success' => false,
                'errors' => ["Invalid Credentials"]
            ]
        ], 401);
    }
}
