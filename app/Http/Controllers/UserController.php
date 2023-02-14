<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Image;
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
                // $array['error'] = 'Data de nascimento inválida.';
                // return $array;
                return response()->json(['error' => 'Data de nascimento inválida.'], 400);
            }

            //verificar existencia do email
            $emailExists = User::where('email', $email)->count();
            if($emailExists > 0){
                // $array['error'] = 'E-mail já cadastrado.';
                // return $array;
                return response()->json(['error' => 'E-mail já cadastrado.'], 400);
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
                // $array['error'] = 'Opss.. Ocorreu um erro.';
                // return $array;
                return response()->json(['error' => 'Opss.. Ocorreu um erro.'], 500);
            }

            $array['token'] = $token;

        }else{
            // $array['error'] = 'Preencha todos os campos.';
            // return $array;
            return response()->json(['error' => 'Preencha todos os campos.'], 400);
        }

        return $array;
    }

    public function read()
    {
        $user = $this->loggedUser;
        return response()->json(['user' => $user]);
    }

    public function update(Request $request)
    {
        $array = ['error' => ''];
        $user = User::find($this->loggedUser->id);

        $name = $request->name;
        $email = $request->email;
        $birthdate = $request->birthdate;
        $city = $request->city;
        $work = $request->work;
        $password = $request->password;
        $passowrd_confirm = $request->passowrd_confirm;


        if($name){
            $user->name = $name;
        }

        if($city){
            $user->city = $city;
        }

        if($work){
            $user->work = $work;
        }

        if($email){
            if($email !== $user->email){
                $emailExists = User::where('email', $email)->count();
                if ($emailExists > 0) {
                    return response()->json(['error' => 'E-mail já cadastrado.'], 400);
                }
                $user->email = $email;
            }
        }

        if($birthdate){
            if (strtotime($birthdate) === false) {
                return response()->json(['error' => 'Data de nascimento inválida.'], 400);
            }
            $user->birthdate = $birthdate;
        }

        if($password && $passowrd_confirm){
            if($password !== $passowrd_confirm){
                return response()->json(['error' => 'Senhas não batem.'], 400);
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user->password = $hash;
        }

        $user->save();
        $array['user'] = $user;
        return $array;
        // return response()->json([$array,$user],200);
    }

    public function updateAvatar(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg','image/jpeg','image/png'];

        $image = $request->file('avatar');
        $user = User::find($this->loggedUser->id);

        if(empty($image)){
            return response()->json(['error' => 'Arquivo não enviado.'],400);
        }

        if(in_array($image->getClientMimeType(), $allowedTypes)){
            $ext = $request->file('avatar')->extension();
            $filename = md5(time() . rand(0, 9999)) . '.' . $ext;
            $destPath = public_path('/medias/avatars');

            // //Apagar foto antiga
            $photoOld = '/' . $user->avatar;
            if (file_exists($destPath.$photoOld) && $user->avatar != 'avatar.jpg') {
                unlink($destPath.$photoOld);
            }

            //salvar / atualizar foto
            $img = Image::make($image->path())->fit(200,200)->save("{$destPath}/{$filename}");
            $user->avatar = $filename;
            $user->save();

            $array['url'] = url("/medias/avatars/{$filename}");
            // $array['url'] = $filename;

        }else{
            return response()->json(['error' => 'Arquivo não suportado.'],400);
        }

        return $array;
    }

    public function updateCover(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('cover');
        $user = User::find($this->loggedUser->id);

        if (empty($image)) {
            return response()->json(['error' => 'Arquivo não enviado.'], 400);
        }

        if (in_array($image->getClientMimeType(), $allowedTypes)) {
            $ext = $request->file('cover')->extension();
            $filename = md5(time() . rand(0, 9999)) . '.' . $ext;
            $destPath = public_path('/medias/covers');

            // //Apagar foto antiga
            $photoOld = '/' . $user->cover;
            if (file_exists($destPath . $photoOld) && $user->cover != 'cover.jpg') {
                unlink($destPath . $photoOld);
            }

            //salvar / atualizar foto
            $img = Image::make($image->path())->fit(850, 310)->save("{$destPath}/{$filename}");
            $user->cover = $filename;
            $user->save();

            $array['url'] = url("/medias/covers/{$filename}");
            // $array['url'] = $filename;
        } else {
            return response()->json(['error' => 'Arquivo não suportado.'], 400);
        }

        return $array;
    }
}
