<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
        UsersController
        ,SectionController
        ,CategoryController
        ,EquipmentController
        ,WarehouseController
        ,WorkspaceController
        ,IngredentsController
    };
use App\Http\Controllers\Auth\{AuthController,SocialLoginController};


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
    
    Route::prefix('user')->controller(UsersController::class)->group(function () {
        Route::get('/profile','profile');
        Route::put('/update','update');
        Route::delete('/destroy','destroy');
    });


    Route::apiResource('workspaces', WorkspaceController::class);
    Route::prefix('workspaces/{id}')->group(function () {
        Route::get('categories',[CategoryController::class,'index']);
        Route::get('sections',[SectionController::class,'index']);
        Route::get('warehouse',[WarehouseController::class,'index']);
        Route::get('ingredients',[IngredentsController::class,'index']);
        Route::get('equipments',[EquipmentController::class,'index']);
    });

    Route::apiResource('category',CategoryController::class)->except(['index']);

    Route::apiResource('section',SectionController::class)->except(['index']);
    
    Route::apiResource('warehouse',WarehouseController::class)->except(['index','show']);
    
    Route::apiResource('ingredient',IngredentsController::class)->except(['index']);
    
    Route::apiResource('equipment',EquipmentController::class)->except(['index']);
});