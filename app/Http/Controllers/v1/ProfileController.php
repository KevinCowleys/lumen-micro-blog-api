<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Block;
use App\Models\Follower;
use App\Models\Mute;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        $user = User::select('id', 'username', 'name', 'location', 'gender', 'birth_date', 'website', 'bio', 'created_at')
            ->firstWhere('username', '=', $request->route()[2]['username']);

        if (!$user)
            return response()->json('', 422);

        if ($token = JWTAuth::getToken())
            $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        if (!isset($auth_id) || $user->id == $auth_id) {
            return response()->json($user, 200);
        } else {
            $is_viewable = !Block::where('blocked', '=', $user->id)
                ->where('blocked_by', '=', $auth_id)
                ->first();

            if (!$is_viewable)
                return response()->json(401);

            $user['is_following'] = Follower::where('following', '=', $auth_id)
                ->where('follower', '=', $user->id)->exists();
            $user['is_followed_by'] = Follower::where('following', '=', $user->id)
                ->where('follower', '=', $auth_id)->exists();
            $user['is_blocked'] = Block::where('blocked', '=', $user->id)
                ->where('blocked_by', '=', $auth_id)->exists();
            $user['is_muted'] = Mute::where('muted', '=', $user->id)
                ->where('muted_by', '=', $auth_id)->exists();

            return response()->json($user, 200);
        }
    }

    public function settings(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user = User::select('id', 'username', 'name', 'location', 'gender', 'birth_date', 'website', 'bio', 'email', 'created_at')
            ->firstWhere('id', '=', $auth_id);

        return response()->json($user, 200);
    }

    public function updateSettings(Request $request)
    {
        // return response()->json($request, 200);
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        //validate incoming request 
        $this->validate($request, [
            'user.username' => "required|unique:users,username,{$auth_id}",
            'user.name' => 'required|string',
            'user.location' => 'string',
            'user.gender' => 'string',
            'user.birth_date' => 'required|date',
            'user.website' => 'string',
            'user.bio' => 'string',
            'user.email' => "required|email|unique:users,email,{$auth_id}",
        ]);

        $user = User::firstWhere('id', '=', $auth_id);

        $user['username'] = $request->input('user.username');
        $user['name'] = $request->input('user.name');
        $user['location'] = $request->input('user.location');
        $user['gender'] = $request->input('user.gender');
        $user['birth_date'] = $request->input('user.birth_date');
        $user['website'] = $request->input('user.website');
        $user['bio'] = $request->input('user.bio');
        $user['email'] = $request->input('user.email');

        $user->save();

        return response()->json('', 204);
    }
}
