<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\WarehouseController;
use App\Models\Warehouse;

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


Route::post('auth/register',[AuthController::class,'register']);
Route::post('auth/login',[AuthController::class,'login']);
Route::get('auth/{provider}/redirect',[SocialLoginController::class,'redirect']);
Route::get('auth/{provider}/callback',[SocialLoginController::class,'callBack']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout',[AuthController::class,'logout']);
    
    Route::get('/user/profile',[UsersController::class,'profile']);
    Route::put('/user/update',[UsersController::class,'update']);
    Route::delete('/user/destroy',[UsersController::class,'destroy']);

    Route::apiResource('workspaces', WorkspaceController::class);

    Route::get('/workspaces/{id}/categories',[CategoryController::class,'index']);
    Route::apiResource('category',CategoryController::class)->except(['index']);

    Route::get('/workspaces/{id}/sections',[SectionController::class,'index']);
    Route::apiResource('section',SectionController::class)->except(['index']);
    
    Route::get('/workspaces/{id}/warehouse/{type}',[WarehouseController::class,'index']);
    Route::apiResource('warehouse',WarehouseController::class)->except(['index','show']);

});