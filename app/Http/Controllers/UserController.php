<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['create']]);
        $this->loggedUser = auth()->user();
    }

    public function create()
    {
    }

    public function read()
    {
        return response()->json(['teste' => 'teste Usuário']);
    }

    public function update()
    {
    }

    public function updateAvatar()
    {
    }

    public function updateCover()
    {
    }
}
