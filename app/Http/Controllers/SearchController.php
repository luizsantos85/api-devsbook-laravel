<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function search(Request $request)
    {
        if (!$request->txt) {
            return response()->json(['error' => 'Dado não enviado.'], 400);
        }

        //Busca de usuarios
        $userList = User::where('name', 'LIKE',"%{$request->txt}%")->select('id','name','avatar')->get();
        if(!$userList){
            return response()->json(['error' => 'Nenhum usuário encontrado.'], 400);
        }

        foreach ($userList as $user) {
            $user->avatar = url('media/avatars/' . $user->avatar);
        }
        
        //Busca de post

        return response()->json(['userList' => $userList], 200);
    }
}
