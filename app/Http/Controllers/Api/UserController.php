<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPartnerRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\APIResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    use APIResponse;
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request){
        if($request->name){
            $user = User::where('name', $request->name)->first();
        }else {
            $user = User::where('id', $request->id)->first();
        }
        if($user){
            $token = JWTAuth::fromUser($user);
            $data = $this->createNewToken($token, $user);

            return $this->sendResponse($data, 'login success');
        }


        return $this->sendError('Unauthorized', null,401);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request) {

        $user = User::create(
            $request->validated(),
        );


        $token = JWTAuth::fromUser($user);
        $data = $this->createNewToken($token, $user);

        return $this->sendResponse($data, 'User successfully registered');
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth('api')->logout();

        return $this->sendResponse(null, 'User successfully signed out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {

        return $this->sendResponse($this->createNewToken(auth('api')->refresh()), 'done created new token');
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {

        return $this->sendResponse(auth('api')->user(), 'User profile');

    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $user = null){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user ?? auth('api')->user()
        ]);
    }

    public function addPartner(AddPartnerRequest $request)
    {
        if($request->partner_id == auth('api')->id()){
            return $this->sendError(' you can not be partener with yourself.', null,400);
        }

        $user = auth('api')->user();
        $user->partner_id = $request->partner_id;
        $user->save();

        return $this->sendResponse(null, 'done updated successfully');
    }



}
