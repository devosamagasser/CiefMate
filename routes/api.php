<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Auth\SocialLoginController;

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
Route::get('auth/{provider}/redirect',[SocialLoginController::class,'redirect']);
Route::get('auth/{provider}/callback',[SocialLoginController::class,'callBack']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout',[AuthController::class,'logout']);
    Route::get('/user/profile',[UsersController::class,'profile']);
    Route::post('/user/update',[UsersController::class,'update']);
    Route::delete('/user/destroy',[UsersController::class,'destroy']);
    Route::apiResource('workspaces', WorkspaceController::class);
});