<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;
use Tymon\JWTAuth\Facades\JWTAuth;

class ConversationController extends Controller
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

        $conversations = Conversation::with('sender', 'recipient')->skip($request->get('offset') * 100)->take(100)->where('sender_id', '=', $auth_id)->orWhere('recipient_id', '=', $auth_id)->orderBy('updated_at', 'DESC')->get();

        return response()->json($conversations, 200);
    }

    public function create(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];

        // Checks to see if recipient exists
        $recipient_check = User::firstWhere('id', '=', $request->get('recipient_id'));
        if (!$recipient_check) {
            return response()->json('', 422);
        }

        $conversation = Conversation::with('sender', 'recipient')->where(
            fn ($query) =>
            $query->where('sender_id', '=', $auth_id)
                ->where('recipient_id', '=', $request->get('recipient_id'))
        )->orWhere(fn ($query) =>
        $query->where('sender_id', '=', $request->get('recipient_id'))
            ->where('recipient_id', '=', $auth_id))->get();

        if (count($conversation)) {
            return response()->json($conversation, 200);
        } else {
            $conversation = Conversation::create([
                'sender_id' => $auth_id,
                'recipient_id' => $request->get('recipient_id'),
            ]);
            return response()->json($conversation, 201);
        }
    }
}
