<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       try{
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'number' => 'required|string|unique:users,number',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'password' => 'required|string|min:6|confirmed',
            ]);
    
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
                'image' => $imageName,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
    
            $token = JWTAuth::fromUser($user);

            return response()->json(['user' => $user, 'token' => $token], 201);
        
       }catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
       }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    
}

    