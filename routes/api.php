<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\SocialAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('auth/register',[AuthController::class,'register']);
Route::post('auth/login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->delete('/logout',[AuthController::class,'logout']);

Route::get('auth/{provider}/redirect',[SocialLoginController::class,'redirect']);
Route::get('auth/{provider}/callback',[SocialLoginController::class,'callBack']);