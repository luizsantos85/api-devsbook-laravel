<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostsLike;

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
        if(!$postExists){
            return response()->json(['error' => 'Post inexistente.'], 404);
        }

        //verificar se ja dei like no post
        $postLike = PostsLike::where('post_id', $postExists->id)->where('user_id', $this->loggedUser->id);
        $isLiked = $postLike->count();
        if($isLiked > 0){
            $pl = $postLike->first();
            $pl->delete();

            // $array['isLiked'] = false;
            $isLiked = false;
        }else{
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

        //

    }

}
