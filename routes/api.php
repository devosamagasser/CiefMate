<?php

use App\Modules\{Auth\AuthController,
    Auth\SocialLoginController,
    Categories\CategoryController,
    Equipments\EquipmentController,
    Ingredients\IngredentsController,
    Invitations\InvitationController,
    Members\MemberController,
    Sections\SectionController,
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

    Route::post('/invitation',[InvitationController::class,'invite']);

    Route::apiResource('member',MemberController::class)->only(['index','update']);
    Route::delete('member/{id}/fire',[MemberController::class,'fire']);

});
Route::get('invitation/{id}/accept',[InvitationController::class,'accept'])->name('accept.invitation');

