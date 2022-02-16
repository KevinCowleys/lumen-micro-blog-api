<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Block;
use App\Models\Mute;
use App\Models\Post;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
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
        } else {
            return Post::orderBy('id', 'DESC')
                ->skip($request->get('offset') * 100)
                ->take(100)
                ->get();
        }

        $blocked_ids = Block::where('blocked_by', '=', $auth_id)->get()->pluck('blocked')->toArray();
        $blocked_by_ids = Block::where('blocked', '=', $auth_id)->get()->pluck('blocked_by')->toArray();
        $muted_ids = Mute::where('muted_by', '=', $auth_id)->get()->pluck('blocked_by')->toArray();
        $avoid_posts = array_unique($blocked_ids + $blocked_by_ids + $muted_ids);

        return Post::whereNotIn('user_id', $avoid_posts)
            ->orderBy('id', 'DESC')
            ->skip($request->get('offset') * 100)
            ->take(100)
            ->get();
    }

    public function create(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $this->validate($request, [
            'post.content' => "required|string",
        ]);

        $post = Post::create([
            'content' => $request->input('post.content'),
            'user_id' => $auth_id
        ]);

        return response()->json($post, 201);
    }

    public function destroy(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $posts = User::with('posts')
            ->firstWhere('id', '=', $auth_id);
        $post = $posts->posts->where('id', '=', $request->route()[2]['post_id']);

        if (!$post->count())
            return response()->json('', 422);

        foreach($post as $action) {
            $action->delete();
            return response()->json('', 204);
        }
    }
}
