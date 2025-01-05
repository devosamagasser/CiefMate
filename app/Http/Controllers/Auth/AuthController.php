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
use App\Http\Requests\RegisterRequest;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @tags Auth
 */
class AuthController extends Controller
{

    public function __construct(public AuthServices $authServices)
    {
        
    }

    /**
     * Login
     * @param LoginRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only(['email','password']);

            if (! $user = $this->checkUser($credentials)) {
                return ApiResponse::notFound('Your credentials doesn\'t match our records');                      
            }

            $token = $this->authServices->generateToken($user);
            return$this->authServices->respondWithToken($user,$token);

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Your credentials doesn\'t match our records');            

        }  catch (\Exception $e) {
            return ApiResponse::serverError('error occured please try again');  

        }
    }

    /**
     * Register
     * @param RegisterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        try{
            $userData = $this->authServices->prepareUserData($request);

            $user = User::create($userData);
            
            $user->notify(new EmailVerificationNotification());

            return ApiResponse::created($user,'User created successfully');
            
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());            
        }

    }


    /**
     * Logout
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
       try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::message('Logout successfully');
        } catch (Exception $e) {
            return ApiResponse::serverError();            
        }
    }


    /**
     * @param $credentials
     * @return mixed
     * @throws \Exception
     */
    protected function checkUser($credentials): mixed
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
