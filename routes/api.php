<?php

use App\Http\Middleware\RegisteredInWorkspaceMiddleware;
use App\Modules\{Auth\AuthController,
    Auth\SocialLoginController,
    Categories\CategoryController,
    Equipments\EquipmentController,
    Ingredients\IngredentsController,
    Invitations\InvitationController,
    Members\MemberController,
    Recipes\RecipeController,
    RecipesComments\RecipeCommentsController,
    Users\UsersController,
    Warehouses\WarehouseController,
    Workspaces\WorkspaceController};
use App\Modules\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;


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


Route::middleware('guest')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('otp-verification', [EmailVerificationController::class, 'otpVerification']);
    Route::post('resend-otp', [EmailVerificationController::class, 'reSendOtp']);
    Route::get('auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
    Route::get('auth/{provider}/callback', [SocialLoginController::class, 'callBack']);
});

Route::middleware(['auth:sanctum','verified'])->group(function () {
    Route::delete('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

    Route::prefix('user')->controller(UsersController::class)->group(function () {
        Route::get('/profile','profile');
        Route::put('/update','update');
        Route::delete('/destroy','destroy');
    });

    Route::post('workspaces', [WorkspaceController::class,'store']);
    Route::get('workspaces/{workspace}', [WorkspaceController::class,'show']);
    Route::middleware(RegisteredInWorkspaceMiddleware::class)->group(function () {
        Route::apiResource('workspaces', WorkspaceController::class)->except(['store','show']);
        Route::apiResource('category',CategoryController::class);
        Route::apiResource('section',RecipeCommentsController::class);
        Route::apiResource('warehouse',WarehouseController::class)->except(['show']);
        Route::apiResource('ingredient',IngredentsController::class);
        Route::apiResource('equipments',EquipmentController::class);
        Route::apiResource('recipes',RecipeController::class)->except(['update']);
        Route::post('recipes/{recipe}',[RecipeController::class,'update']);
        Route::post('/invitation',[InvitationController::class,'invite']);
        Route::apiResource('member',MemberController::class)->except(['store','destroy']);
        Route::delete('member/{id}/fire',[MemberController::class,'fire']);
        Route::post('comments',[RecipeCommentsController::class,'store']);
        Route::get('comments/{recipe}',[RecipeCommentsController::class,'show']);
    });
});
Route::get('invitation/{id}/accept',[InvitationController::class,'accept'])->name('accept.invitation');

