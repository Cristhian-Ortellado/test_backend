<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\LoginInvalidResource;
use App\Http\Resources\LoginSuccessResource;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginUserRequest $request)
    {
        //validate fields
        $username = $request->input('username');
        $password = $request->input('password');

        //try to login
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = $this->userRepository->findByUsername($username);

            //update last login
            $user->last_login = now();
            $user->save();

            return LoginSuccessResource::make($user);
        };

        //return Invalid Credentials instead of the username of the user because client never should know if the user exists or not
        return LoginInvalidResource::make(['errors'=>["Invalid Credentials"]])
            ->response()
            ->setStatusCode(401);
    }
}
