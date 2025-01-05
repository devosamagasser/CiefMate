<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Facades\ApiResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Laravel\Socialite\Facades\Socialite;
use Dedoc\Scramble\Attributes\ExcludeRouteFromDocs;


/**
 * @tags Auth
 */
class SocialLoginController extends Controller
{

    public function __construct(public AuthServices $authServices)
    {
        
    }

    public $providers = ['google'];

    /**
     * Social Login
     * 
     * providers [google]
     * @param $provider
     */
    public function redirect($provider)
    {
        return (in_array($provider,$this->providers)) ?
            Socialite::driver($provider)->stateless()->redirect()
            : 
            $this->providerNotSupported();
    }

    #[ExcludeRouteFromDocs]
    public function callBack($provider) 
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
            $data = $this->authServices->prepareUserData($user,$provider);
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            $token = $this->authServices->generateToken($user,$data['email']);

            return $this->authServices->respondWithToken($user,$token);

        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }

    }

    private function providerNotSupported()
    {
        return ApiResponse::message(
            'provider not supported',
            Response::HTTP_BAD_REQUEST
        );
    }
}
