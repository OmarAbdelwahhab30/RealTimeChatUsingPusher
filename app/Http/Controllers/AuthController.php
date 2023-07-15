<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)){
            $user = User::where("name",$request->name)->first();

            $user->token = $user->createToken("personal access token")->plainTextToken;

            return $user;
        }
        return  false;
    }

    public function register(Request $request){
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->token = $user->createToken("")->plainTextToken;
        return $user;
    }


    public function logout($request){
        if ($request->user()->currentAccessToken()->delete()){
            return true;
        }
        return false;
    }
}
