<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['login', 'unAuthorized']]);
    }

    public function login(Request $request)
    {
        $array = ['error' =>''];

        $email = $request->input('email');
        $password = $request->input('password');

        if(!$email || !$password){
            return response()->json(['error' => 'Preencha todos os campos.'], 404);
        }

        $token = auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);

        if(!$token){
            return response()->json(['error' => 'E-mail e/ou senha inválidos.'], 400);
        }

        $array['token'] = $token;
        return $array;
        // return response()->json(['error' => '','token' => $token], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['error' => ''],200);
    }

    public function refresh()
    {
        $token = auth()->refresh();
        return response()->json(['error' => '','token' => $token], 200);

    }

    public function unAuthorized()
    {
        return response()->json(['error' =>'Não autorizado.'], 401);
    }

}
