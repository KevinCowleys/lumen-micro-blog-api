<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Follower;
use App\Models\Block;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class FollowerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function showFollowing(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user_visited = User::with('following')->firstWhere('username', '=', $request->route()[2]['username']);

        // Checks to see if user exists
        if (!$user_visited)
            return response()->json('', 422);

        $is_viewable = !Block::where('blocked', '=', $auth_id)
            ->where('blocked_by', '=', $user_visited->id)
            ->first();

        if (!$is_viewable)
            return response()->json(401);

        return response()->json($user_visited->following, 200);
    }

    public function showFollowers(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user_visited = User::with('followers')->firstWhere('username', '=', $request->route()[2]['username']);

        // Checks to see if user exists
        if (!$user_visited)
            return response()->json('', 422);

        $is_viewable = !Block::where('blocked', '=', $auth_id)
            ->where('blocked_by', '=', $user_visited->id)
            ->first();

        if (!$is_viewable)
            return response()->json(401);

        return response()->json($user_visited->follower, 200);
    }

    public function toggleFollow(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user_being_followed = User::firstWhere('username', '=', $request->route()[2]['username']);
        
        // Checks to see if user exists and stops you from following yourself
        if (!$user_being_followed || $user_being_followed->id == $auth_id)
            return response()->json('', 422);

        $is_viewable = !Block::where('blocked', '=', $auth_id)
            ->where('blocked_by', '=', $user_being_followed->id)
            ->first();

        if (!$is_viewable)
            return response()->json(401);

        $follow = User::with('following')->firstWhere('id', '=', $auth_id)->following;
        $follow = Follower::firstOrNew([
            'following' => $user_being_followed->id,
            'follower' => $auth_id,
        ]);

        if ($follow->id == null) {
            $follow->save();
            return response()->json($follow, 201);
        } else {
            $follow->delete();
            return response('', 204);
        }
    }
}
