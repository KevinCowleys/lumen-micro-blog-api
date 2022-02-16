<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout', 'register']]);
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'user.name' => 'required|string',
            'user.email' => 'required|email|unique:users,email',
            'user.birth_date' => 'required|date',
            'user.password' => 'required|confirmed',
        ]);

        try {
            $user = new User;
            $user->username = $this->find_unique_username($request->input('user.name'));
            $user->name = $request->input('user.name');
            $user->email = $request->input('user.email');
            $user->birth_date = $request->input('user.birth_date');
            $plainPassword = $request->input('user.password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            $request->merge(['email' => $request->input('user.email')]);
            $request->merge(['password' => $request->input('user.password')]);
            $request->offsetUnset('user');

            return $this->login($request);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => $e], 409);
        }
    }

    function find_unique_username(String $username)
    {
        $name = explode(' ', trim($username))[0];
        $new_username = $name;
        $i = 0;

        while (User::firstWhere('username', '=', $new_username)?->exists()) {
            $i++;
            $new_username = $name . rand(pow(10, 8 - 1), pow(10, 8) - 1);
        }

        return $new_username;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
