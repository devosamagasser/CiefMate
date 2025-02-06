<?php

namespace App\Modules\Users;

use App\Facades\ApiResponse;
use App\Facades\FileHandeler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits;
use App\Modules\Users\Requests\UserUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for managing user profile"
 * )
 */
class UsersController extends Controller
{
    use Traits\ControllerTraits;
    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     summary="Get user profile",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
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
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="code", type="integer", example=404)
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
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            return ApiResponse::success(new UserResources($user));

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('User not found');

        }  catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/update",
     *     summary="Update user profile",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", example="010101010"),
     *             @OA\Property(property="avatar", type="string", format="binary", example="image.png"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
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
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
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
    public function update(UserUpdateRequest $request)
    {
        try {
            $user = $request->user();
            $data = $this->updateWithFile('avatar', $request, $user, 'avatars');

            $user->fill($data);
            if($user->isDirty()){
                $user->save();
                return ApiResponse::success(new UserResources($user), 'Profile updated successfully');
            }
            return ApiResponse::message('No change');

        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to update profile, please try again.');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/destroy",
     *     summary="Delete user profile",
     *     tags={"User"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="deleted successfully"),
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
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        try {
            $user = $request->user();
            $user->delete();
            FileHandeler::deleteFile($user->avatar);
            return ApiResponse::message('Deleted successfully');

        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to delete profile, please try again.');
        }
    }

}
