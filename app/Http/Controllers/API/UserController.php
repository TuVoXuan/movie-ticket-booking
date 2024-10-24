<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends BaseController
{
    public function signUp(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'email' => 'required|string|email|unique:users,email',
                'account' => 'required|string|min:4|unique:users,account',
                'phone' => 'required|string|unique:users,phone',
                'password' => ['required', 'string', Password::min(8)->numbers()->letters()->mixedCase()->symbols()]
            ]);

            if ($validated->fails()) {
                return $this->sendError($validated->errors());
            }

            $password = Hash::make($body['password']);

            User::create([
                'email' => $body['email'],
                'password' => $password,
                'account' => $body['account'],
                'phone' => $body['phone']
            ]);

            return $this->sendResponse('', 'Sign up successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return  $this->sendError('An error occurred during sign up.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function signIn(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'account' => 'required|string',
                'password' => ['required', 'string', Password::min(8)->numbers()->letters()->mixedCase()->symbols()]
            ]);

            if ($validated->fails()) {
                return $this->sendError($validated->errors());
            }

            $user = User::where('account', $body['account'])->first();
            Log::info($user->toArray());
            Log::info($user->password);
            if (!$user || !Hash::check($body['password'], $user->password)) {
                return $this->sendError('Invalid account or password', [], 400);
            }

            $token = JWTAuth::fromUser($user);

            return $this->sendResponse([
                'user' => $user,
                'token' => $token
            ], 'Sign in successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return  $this->sendError('An error occurred during sign up.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getInfo(Request $request, string $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return $this->sendError('User not found.', [], Response::HTTP_NOT_FOUND);
            }
            return $this->sendResponse($user, 'Get user info successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return  $this->sendError('An error occurred during get user info.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
