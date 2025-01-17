<?php

namespace App\Http\Controllers\Auth;

use App\Facades\ApiResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 */
class SocialLoginController extends Controller
{
    public function __construct(public AuthServices $authServices, private Socialite $socialite)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/auth/{provider}/redirect",
     *     summary="Redirect to provider for social login",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"google", "facebook", "github"})
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to provider's login page"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Provider not supported",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider not supported"),
     *             @OA\Property(property="code", type="integer", example=400)
     *         )
     *     )
     * )
     */
    public function redirect($provider)
    {
        try {

            if (!in_array($provider, config('services.socialite_providers') )) {
                return $this->providerNotSupported();
            }
            
            return $this->socialite::driver($provider)->stateless()->redirect();
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/{provider}/callback",
     *     summary="Handle provider callback",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"google", "facebook", "github"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Provider not supported",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider not supported"),
     *             @OA\Property(property="code", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to process this action, please try again."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function callBack($provider)
    {
        try {
            if (!in_array($provider, config('services.socialite_providers'))) {
                return $this->providerNotSupported();
            }

            $providerUser = $this->socialite::driver($provider)->stateless()->user();
            return $this->authServices->handleSocialLogin($providerUser, $provider);

        } catch (InvalidStateException $e) {
            return ApiResponse::message('Invalid state. Please try again.', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    private function providerNotSupported()
    {
        return ApiResponse::message(
            'Provider not supported',
            Response::HTTP_BAD_REQUEST
        );
    }
}
