<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mute;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class MuteController extends Controller
{
    public function index(Request $request)
    {
        $token = JWTAuth::getToken();
        if ($token) {
            $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        } else {
            return response()->json('', 401);
        }

        $mutes = Mute::with('muted')->where('muted_by', '=', $auth_id)
            ->skip($request->get('offset') * 100)
            ->take(100)
            ->get();

        return response()->json($mutes);
    }

    public function toggleMute(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user_being_muted = User::firstWhere('username', '=', $request->route()[2]['username']);
        
        // Checks to see if user exists and stops you from muting yourself
        if (!$user_being_muted || $user_being_muted->id == $auth_id)
            return response()->json('', 422);

        $mute = Mute::firstOrNew(['muted' => $user_being_muted->id]);

        if ($mute->id == null) {
            $mute->muted_by = $auth_id;
            $mute->save();
            return response()->json($mute, 201);
        } else {
            $mute->delete();
            return response('', 204);
        }
    }
}
