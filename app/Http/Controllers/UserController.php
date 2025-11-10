<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     public function index()
    {
        $user=User::all();
        return response()->json($user);
    }


    public function register(RegisterRequest $request)
    {
        $user=User::create($request->validated());
        return response()->json($user);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        if(!Auth::attempt($request->only('email','password'))){
             return response()->json(['message'=>'email or password invalide']);
        }

        $user=User::where('email', $request->email)->first();

        $token=$user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'=>'login is valide',
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logout successful']);

    }
}
