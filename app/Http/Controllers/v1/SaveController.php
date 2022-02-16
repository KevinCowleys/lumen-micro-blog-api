<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\Post;
use App\Models\PostSaved;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $token = JWTAuth::getToken();
        if ($token) {
            $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        } else {
            return response()->json('', 401);
        }

        $user = User::firstWhere('username', '=', $request->route()[2]['username']);

        if ((isset($user->id) && isset($auth_id)) && ($user->id == $auth_id)) {
            $saves = PostSaved::where('user_id', '=', $auth_id)->orderBy('created_at', 'DESC')->pluck('post_id');
            $posts = Post::whereIn('id', $saves)->skip($request->get('offset') * 100)->take(100)->get();
            return response()->json($posts, 200);
        } else {
            return response()->json('', 401);
        }

        $blocks = Block::where('blocked_by', '=', $auth_id)
            ->skip($request->get('offset') * 100)
            ->take(100)
            ->get();

        return response()->json($blocks);
    }

    public function toggleSave(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $post = Post::firstWhere('id', '=', $request->route()[2]['post_id']);
        
        // Checks to see if the post exists
        if (!$post)
            return response()->json('', 422);

        $post_save = PostSaved::firstOrNew([
            'user_id' => $auth_id,
            'post_id' => $post->id,
        ]);

        if ($post_save->id == null) {
            $post_save->save();
            return response()->json($post_save, 201);
        } else {
            $post_save->delete();
            return response('', 204);
        }
    }
}
