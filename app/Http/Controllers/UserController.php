<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['create']]);
        $this->loggedUser = auth()->user();
    }

    public function create(Request $request)
    {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $birthdate = $request->input('birthdate');

        if($name && $email && $password && $birthdate){
            //validar data de nascimento
            if(strtotime($birthdate) === false){
                $array['error'] = 'Data de nascimento inválida.';
                return $array;
            }

            //verificar existencia do email
            $emailExists = User::where('email', $email)->count();
            if($emailExists > 0){
                $array['error'] = 'E-mail já cadastrado.';
                return $array;
            }
            //cria novo usuário
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $newUser = new User;
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->birthdate = $birthdate;
            $newUser->password = $hash;
            $newUser->save();

            $token = auth()->attempt([
                'email' => $email,
                'password' => $password
            ]);

            if(!$token){
                $array['error'] = 'Opss.. Ocorreu um erro.';
                return $array;
            }

            $array['token'] = $token;
        }else{
            $array['error'] = 'Preencha todos os campos.';
            return $array;
        }
        return $array;
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
