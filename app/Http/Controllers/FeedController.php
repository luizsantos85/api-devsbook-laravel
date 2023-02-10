<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Image;

class FeedController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function read()
    {

    }

    public function userFeed()
    {

    }

    public function create(Request $request)
    {
        //(type=text/photo, body/photo)
        $array = ['error' => ''];

        $type = $request->input('type');
        $body = $request->input('body');
        $photo = $request->file('photo');

        if(!$type){
            return response()->json(['error' => 'Dados não enviados.'], 400);
        }

        switch($type){
            case 'text':
                if (!$body) {
                    return response()->json(['error' => 'Texto não enviado.'], 400);
                }
            break;
            case 'photo':
                if (!$photo) {
                    return response()->json(['error' => 'Dados não enviados.'], 400);
                }
                $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
                if(in_array($photo->getClientMimeType(), $allowedTypes)){
                    $ext = $request->file('photo')->extension();
                    $filename = md5(time() . rand(0, 9999)) . '.' . $ext;
                    $destPath = public_path('/medias/uploads');

                    Image::make($photo->path())->resize(800,null, function($constraint){
                        $constraint->aspectRatio();
                    })->save("{$destPath}/{$filename}");

                    $body = $filename;
                }else{
                    return response()->json(['error' => 'Arquivo não suportado.'], 400);
                }
            break;
            default:
                return response()->json(['error' => 'Tipo de postagem inexistente.'], 400);
            break;
        }

        if($body){
            $newPost = new Post;
            $newPost->user_id = $this->loggedUser->id;
            $newPost->type = $type;
            $newPost->body = $body;
            $newPost->created_at = date('Y-m-d H:i:s');
            $newPost->save();

            $array['post'] = $newPost;
        }

        return $array;
    }
}
