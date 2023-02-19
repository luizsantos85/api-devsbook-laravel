<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostsLike;
use App\Models\PostsComment;

class PostController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function like($id)
    {
        // $array = ['error' => ''];

        //verificar se o post existe
        $postExists = Post::find($id);
        if (!$postExists) {
            return response()->json(['error' => 'Post inexistente.'], 404);
        }

        //verificar se ja dei like no post
        $postLike = PostsLike::where('post_id', $postExists->id)->where('user_id', $this->loggedUser->id);
        $isLiked = $postLike->count();
        if ($isLiked > 0) {
            $pl = $postLike->first();
            $pl->delete();

            // $array['isLiked'] = false;
            $isLiked = false;
        } else {
            $newPostLike = new PostsLike;
            $newPostLike->post_id = $postExists->id;
            $newPostLike->user_id = $this->loggedUser->id;
            $newPostLike->created_at = now();
            $newPostLike->save();

            // $array['isLiked'] = true;
            $isLiked = true;
        }
        $totalLikes = PostsLike::where('post_id', $postExists->id)->count();
        // $array['totalLikes'] = $totalLikes;

        // return $array;
        return response()->json(['totalLikes' => $totalLikes, 'isLiked' => $isLiked], 200);
    }

    public function comment(Request $request, $id)
    {
        //verifica see o post existe
        $postExists = Post::find($id);
        if (!$postExists) {
            return response()->json(['error' => 'Post inexistente.'], 404);
        }
        if (!$request->txt) {
            return response()->json(['error' => 'Comentário não enviado.'], 400);
        }

        $newPostComment = new PostsComment;
        $newPostComment->post_id = $postExists->id;
        $newPostComment->user_id = $this->loggedUser->id;
        $newPostComment->created_at = now();
        $newPostComment->body = $request->txt;
        $newPostComment->save();
        
        return response()->json(['Post' => $newPostComment], 200);
    }
}
