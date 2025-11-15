<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     public function index()
    {
        $user=User::where('role','animateur')->get();

        return response()->json($user);
    }
     public function show(User $id){
        if($id->role !== 'animateur'){
            return response()->json(['message'=>'pas animateur']);
        }
      return response()->json($id);
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

    public function resetPassword(LoginRequest $request){
        $request->validated();
        $user=User::where('email',$request->email)->first();
        if(!$user){
            return response()->json(['message' =>'email not found']);
        }
        $user->update([
           'password'=> $request->password
        ]);
        return response()->json(['message' => 'Password successfully reset']);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logout successful']);

    }

    public function update(UpdateRegister $request, User $id){
       $data= $request->validated();
       if($id->role!=='animateur'){
        return response()->json(['message'=>'pas animateur']);
       }
       $id->update($data);
       return response()->json($id);

    }
    public function destory(User $id){
        if($id->role!=='animateur'){
        return response()->json(['message'=>'pas animateur']);
       }
       $id->delete();
       return response()->json(['message'=>'animateur est supprimer']);
    }
}
