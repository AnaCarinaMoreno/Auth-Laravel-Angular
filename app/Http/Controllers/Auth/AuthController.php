<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request){
       $validator = Validator::make($request->all(),[
        'name'=> 'required|string|max:225',
        'email'=> 'required|string|email|unique:users',
        'password'=> 'required|string|min:6|confirmed'

       ]); 

       if($validator->fails()){

        return response()->json($validator->errors(),400);

       }

       $user = User::create([
          'name'=> $request->name,
          'email'=> $request->email,
          'password'=>Hash::make($request->password)
       ]);

       return response()->json([
        'message' => '¡Usuario creado con éxito!',
        'user' => $user

       ]);

       
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


}
