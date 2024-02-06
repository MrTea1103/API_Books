<?php

namespace App\Http\Controllers;

use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|regex:/^[a-zA-ZáàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệóòỏõọôốồổỗộơớờởỡợíìỉĩịúùủũụưứừửữựýỳỷỹỵđĐ\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:3',
        ]);

        $data = new users();
        $data->fill($request->only(['name', 'email']));
        $data->password = Hash::make($request->password);
        $data->role = 'user';
        $data->save();


        $expiresIn = Carbon::now()->addWeek()->timestamp;
        // Generate JWT token for the newly registered user
        $token = JWTAuth::fromUser($data);

        return response()->json([
            'input' => $data,
            'message' => 'Thêm thành công!',
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $data,
            'expires_in' => $expiresIn
        ], 200);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->jsonResponse($token);
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
        try {
            $token = JWTAuth::parseToken()->refresh();
            return $this->jsonResponse($token);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to refresh token'], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse($token)
    {

        $expiresIn = Carbon::now()->addWeek()->timestamp;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'user'         => auth()->user(),
            'expires_in'   => $expiresIn
        ]);
    }
    
}
