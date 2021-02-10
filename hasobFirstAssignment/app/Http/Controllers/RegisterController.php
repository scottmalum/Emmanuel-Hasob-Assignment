<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['registerUser']]);
    }
    //
    public function registerUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $user = User::create($validator->validated());

        return response()->json([
            'message' => "User Successfully added",
            'user' => new PrivateUserResource($user)
        ]);
    }
}
