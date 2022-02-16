<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlockController extends Controller
{
    public function index(Request $request)
    {
        $token = JWTAuth::getToken();
        if ($token) {
            $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        } else {
            return response()->json('', 401);
        }

        $blocks = Block::with('blocked')->where('blocked_by', '=', $auth_id)
            ->skip($request->get('offset') * 100)
            ->take(100)
            ->get();

        return response()->json($blocks);
    }

    public function toggleBlock(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        $user_being_blocked = User::firstWhere('username', '=', $request->route()[2]['username']);
        
        // Checks to see if user exists and stops you from blocking yourself
        if (!$user_being_blocked || $user_being_blocked->id == $auth_id)
            return response()->json('', 422);

        $block = Block::firstOrNew(['blocked' => $user_being_blocked->id]);

        if ($block->id == null) {
            $block->blocked_by = $auth_id;
            $block->save();
            return response()->json($block, 201);
        } else {
            $block->delete();
            return response('', 204);
        }
    }
}
