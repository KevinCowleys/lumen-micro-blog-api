<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        $conversation_id = $request->route()[2]['conversation_id'];

        // Checks to see if conversation exists first
        $conversation = Conversation::with('sender', 'recipient')->where(
            fn ($query) =>
            $query->where('id', '=', $conversation_id)
                ->where('sender_id', '=', $auth_id)
        )->orWhere(fn ($query) =>
        $query->where('id', '=', $conversation_id)
            ->where('recipient_id', '=', $auth_id))->first();

        if (!$conversation) {
            return response()->json('', 422);
        }

        return response()->json($conversation->messages->skip($request->get('offset') * 100)->take(100), 200);
    }

    public function create(Request $request)
    {
        $token = JWTAuth::getToken();
        $auth_id = JWTAuth::getPayload($token)->toArray()['sub'];
        $conversation_id = $request->route()[2]['conversation_id'];

        $this->validate($request, [
            'message.content' => "required|string",
        ]);

        // Checks to see if conversation exists first
        $conversation = Conversation::with('sender', 'recipient')->where(
            fn ($query) =>
            $query->where('id', '=', $conversation_id)
                ->where('sender_id', '=', $auth_id)
        )->orWhere(fn ($query) =>
        $query->where('id', '=', $conversation_id)
            ->where('recipient_id', '=', $auth_id))->get();

        if (!count($conversation)) {
            return response()->json('', 422);
        }

        $message = Message::create([
            'content' => $request->input('message.content'),
            'conversation_id' => $conversation_id,
            'user_id' => $auth_id
        ]);
    
        return response()->json($message, 201);
    }
}
