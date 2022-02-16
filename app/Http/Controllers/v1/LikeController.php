<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $token = JWTAuth::getToken();
        if ($token) {
            $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        }

        $user = User::firstWhere('username', '=', $request->route()[2]['username']);

        if (!isset($auth_id)) {
            $likes = PostLike::where('user_id', '=', $user->id)->orderBy('created_at', 'DESC')->pluck('post_id');
            $posts = Post::whereIn('id', $likes)->skip($request->get('offset') * 100)->take(100)->get();
            return response()->json($posts, 200);
        } else {
            $is_viewable = !Block::where('blocked', '=', $user->id)
                ->where('blocked_by', '=', $auth_id)
                ->first();

            if (!$is_viewable)
                return response()->json(401);

            if (isset($user->id)) {
                $likes = PostLike::where('user_id', '=', $user->id)->orderBy('created_at', 'DESC')->pluck('post_id');
                $posts = Post::whereIn('id', $likes)->skip($request->get('offset') * 100)->take(100)->get();
                return response()->json($posts, 200);
            } else {
                return response()->json('', 422);
            }
        }
    }

    public function toggleLike(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $post = Post::firstWhere('id', '=', $request->route()[2]['post_id']);

        // Checks to see if the post exists
        if (!$post)
            return response()->json('', 422);

        $post_like = PostLike::firstOrNew([
            'user_id' => $auth_id,
            'post_id' => $post->id,
        ]);

        if ($post_like->id == null) {
            $post_like->save();
            return response()->json($post_like, 201);
        } else {
            $post_like->delete();
            return response('', 204);
        }
    }
}
