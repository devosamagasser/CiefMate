<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Facades\ApiResponse;
use App\Facades\FileHandeler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class AuthServices 
{



    public function handleSocialLogin($user,$provider)
    {
        $user = User::where('provider_id',$user->id)->first();
        if ($user) {
            $token = $this->generateToken($user);
            return $this->respondWithToken($user,$token);
        }

        $userData = $this->prepareUserData($user,$provider);
        $user = User::create($userData);
        $token = $this->generateToken($user);
        return $this->respondWithToken($user,$token);
    }

    /**
     * Get the token array structure.
     * @param  string $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function respondWithToken($user,$token)
    {
        return ApiResponse::success([
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'user' => $user
            ]
        ],'logged in successfully');
    }


    /**
     * Generate token for user
     * @param $user
     * @param $email
     * @return string
     */
    public function generateToken($user)
    {
        return $user->createToken('New API Token')->plainTextToken;
    }


    public function prepareUserData ($request,$provider = 'email') 
    {
        $password = $request->password ?? Str::random(8); 
        $avatar = $this->avatarHandeler($request->avatar, $provider);
        
        return [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'provider' => $provider,
            'provider_id' => $request->id,
            'avatar' => $avatar
        ];
    }

    public function avatarHandeler($avatar , $provider) 
    {
        if ($provider != 'email') {
            return $avatar;
        }

        $avatarName = $avatar ? FileHandeler::storeFile($avatar,'images/avatars/') : 'default.png';
        return config('url').'/storage/images/avatars/'.$avatarName;
    }
}