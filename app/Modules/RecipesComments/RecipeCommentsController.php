<?php

namespace App\Modules\RecipesComments;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerTraits;
use App\Modules\RecipesComments\Requests\CommentStoreRequest;
use App\Modules\Sections\Section;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecipeCommentsController extends Controller
{
    use ControllerTraits;
    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Create a new section",
     *     tags={"Comments"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CommentnStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Section created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Comment"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The comment field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function store(CommentStoreRequest $request)
    {
        try {
            $comment = RecipeComment::create([
                'comment' => $request->title,
                'recipe_id' => request()->user()->recipe_id,
                'user_id' => request()->user()->id
            ]);
            return ApiResponse::created(new CommentResource($comment));
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }


   /**
     * @OA\Get(
     *     path="/api/comments/{id}",
     *     summary="Get a comments by recipe ID",
     *     tags={"Comments"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Recipe",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Comment"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comments not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comments not found"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $comments = RecipeComment::with('user','recipe')
                ->where('recipe_id', $id)
                ->get();
            return ApiResponse::success(CommentResource::collection($comments));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }
}
