<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\UsersRelation;
use App\Models\User;
use App\Models\PostsComment;
use Image;
use src\models\UserRelation;

class FeedController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function read(Request $request)
    {
        $array = ['error' => ''];
        $page = intval($request->page);
        $perPage = 2;

        //1. pegar a lista de usuarios q eu sigo.
        $users = [];
        $userList = UsersRelation::where('user_from', $this->loggedUser->id);
        foreach ($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $this->loggedUser->id;

        //2. pegar os posts ordenadoo pela data
        $postList = Post::whereIn('user_id', $users)
            ->orderBy('created_at','desc')
            ->offset($page * $perPage)
            ->limit($perPage)
            ->get();

        $total = Post::whereIn('user_id', $users)->count();
        $pageCount = ceil($total / $perPage);

        //3. preencher as informações adicionais
        $post = $this->postListToObject($postList, $this->loggedUser->id);

        $array['posts'] = [];
        $array['pageCount'] =  $pageCount;
        $array['currentPage'] = $page;

        return $array;
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

    private function postListToObject($postList, $userId)
    {

    }

}
