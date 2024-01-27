<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                'role' => 'required|in:user,admin,super admin',
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
    
            return response()->json(['user' => $user, 'message' => 'User registered successfully'], 201);
        
       }catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
       }
    }
    
}
// public function register(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string',
//             'email' => 'required|email|unique:users',
//             // 'password' => 'required|string|min:6',
//             'number' => ['required', 'string', 'unique:users'],
//             'image' => ['nullable', 'image', 'max:2048'], // Assuming a maximum image size of 2 MB
//             'password' => ['required', 'string', 'min:8', 'confirmed'],
//             'role' => ['string', 'in:user,admin,super admin'], // Adjust roles as needed
//         ]);
        
//         $imagePath = null;

//         if (isset($request['image'])) {
//             $imagePath = $request['image']->store('profile_images', 'public');
//         }

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'image' => $imagePath,
//             'number' => $request->email,
//             'role' => $request->email,  
//         ]);

//         $token = $user->createToken('authToken')->accessToken;

//         return response()->json(['token' => $token], 201);
//     }
    