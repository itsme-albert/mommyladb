<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function getAddress($id)
    {
        $user = User::with('address')->find($id);
        if ($user && $user->address) {
            return response()->json([
                'success' => true,
                'address' => $user->address, 
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User or address not found',
            ]);
        }
    }

    public function createUser(Request $request){
        $formField = $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed'
        ]);
        User::create($formField);
        return response()->json(['message'=>'Registration Success!']);
    }

    public function loginUser(Request $request){
        $request -> validate([
            'email'=>'required|email|exists:users',
            'password'=>'required'
        ]);
        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(['Message'=> 'The provided Credential are Incorrect']);
        }
        $token=$user->createToken($user->last_name)->plainTextToken;
        return response()->json([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'address' => $user->address,
            'token' => $token,
        ]);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message' => 'You are logged out'
        ];
    }
}
