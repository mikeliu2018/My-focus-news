<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

use JWTAuth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);//login, register methods won't go through the api guard
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => '參數錯誤',
                'validator' => $validator->errors()
            ], 400);
        }

        $isExists = User::where('email', '=', $request->email)->exists();
        Log::debug('isExists', [$isExists]);
        if ($isExists === true) {
            return response()->json([
                'status' => false,
                'error' => '帳號已存在'
            ], 403);
        }

        $user = new User();
        $user->name = substr($request->email, 0, strpos($request->email, '@'));
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $result = [
            'status' => true,
            'result' => '註冊成功',
        ];
        return response()->json($result);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => '參數錯誤',
                'validator' => $validator->errors()
            ], 400);
        }

        $result = [
            'status' => false,
            'error' => '帳號或密碼錯誤'
        ];

        if (! $token = auth('api')->attempt($validator->validated())) {
            return response()->json($result, 401);
        }

        $user = auth('api')->user();

        $result = [
            'status' => true,
            'result' => '登入成功',
            'user' => $user,
            'token' => $this->respondWithToken($token),
        ];

        return response()->json($result);
    }

    public function logout()
    {
        auth('api')->logout();
        $result = [
            'status' => true,
            'result' => '登出成功',
        ];
        return response()->json($result);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
    
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 //mention the guard name inside the auth fn
        ];
    }
}
