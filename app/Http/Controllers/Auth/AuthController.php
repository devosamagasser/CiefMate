<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AuthServices;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResources;
use App\Http\Requests\RegisterRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 */
class AuthController extends Controller
{

    public function __construct(public AuthServices $authServices)
    {
        
    }

    /**
    * @OA\Post(
    *     path="/api/auth/login",
    *     summary="User login",
    *     tags={"Auth"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 required={"email", "password"},
    *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
    *                 @OA\Property(property="password", type="string", format="password", example="password"),
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Login successful",
    *         @OA\JsonContent(
    *             @OA\Property(property="code", type="integer", example=200),
    *             @OA\Property(property="message", type="string", example="Logged in successfully"),
    *             @OA\Property(
    *                 property="data",
    *                 type="object",
    *                 @OA\Property(property="access_token", type="string", example="17|e6n7vBgUMKDFUzM5NAZoPE8QkJsp0G4K31DDoS40185d2895"),
    *                 @OA\Property(property="token_type", type="string", example="bearer"),
    *                 @OA\Property(
    *                     property="user",
    *                     type="object",
    *                     ref="#/components/schemas/User"
    *                 )
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation Error",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Validation Error"),
    *             @OA\Property(property="code", type="integer", example=422),
    *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Your credentials don't match our records"),
    *             @OA\Property(property="code", type="integer", example=401)
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
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email','password']);
            
            if (! $user = $this->checkUser($credentials)) {
                return ApiResponse::unAuthrized('Your credentials doesn\'t match our records');            
            }
            
            $token = $this->authServices->generateToken($user);
            
            return $this->authServices->respondWithToken(new UserResources($user),$token);
            
        } catch (ModelNotFoundException $e) {
            return ApiResponse::unAuthrized('Your credentials doesn\'t match our records');            
            
        }  catch (\Exception $e) {
            return ApiResponse::serverError();          
        }
    }
    

   /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "email", "phone", "password", "avatar"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                 @OA\Property(property="phone", type="string", example="0199021098"),
     *                 @OA\Property(property="password", type="string", format="password", example="password"),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     format="binary",
     *                     description="User avatar image",
     *                     example="avatar.jpg"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
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
    public function register(RegisterRequest $request)
    {
        try{
            $userData = $this->authServices->prepareUserData($request);
            
            $user = User::create($userData);
            
            return ApiResponse::created(new UserResources($user),'User created successfully');
            
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());            
        }
        
    }


    /**
     * @OA\Delete(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"Auth"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successfully"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to process this action, please try again."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     ),
     * )
     */
    public function logout(Request $request)
    {
       try {
            $user = $request->user();
            if (is_null($user)) {
                return ApiResponse::unAuthrized();
            }
            $user->currentAccessToken()->delete();
            return ApiResponse::message('Logout successfully');
        } catch (Exception $e) {
            return ApiResponse::serverError();            
        }
    }

    private function checkUser($credentials): mixed
    {
        try {
            $user = User::whereEmail($credentials['email'])->firstOrFail();
            if (! Hash::check($credentials['password'],$user->password))
                return false;
            return $user;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException();
        } catch (Exception $e) {
            throw new \Exception();
        }
    }

}
