<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function signup(UserRequest $request){
        $body = $request->validated();
        DB::beginTransaction();
        try {
           $user = new User();
           $user->name = $body['name'];
           $user->email = $body['email'];
           $user->password = Hash::make($body['password']);
           $user->save();
           $token = $user->createToken('auth_token')->plainTextToken;
           DB::commit();

           return $this->successResponse([
                'token' => $token,
                'user' => new UserResource($user),
            ], 'User created successfully', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function signin(Request $request)
    {
        try {
            $email = User::where('email', $request->email)->first();
            if (!$email) {
                return $this->errorResponse('Email not found', 400);
            }

            $password = $request->only('email', 'password');
            if (!Auth::guard('web')->attempt($password)) {
                return $this->errorResponse('Password incorrect', 400);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'user' => new UserResource($user),
            ], 'User signed in successfully', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = Auth::user();

            return $this->successResponse(new UserResource($user), 'User fetched successfully', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function signout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Successfully signed out', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
