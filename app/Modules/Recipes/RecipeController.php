<?php

namespace App\Modules\Recipes;

use App\Facades\ApiResponse;
use App\Facades\FileHandeler;
use App\Http\Controllers\Controller;
use App\Modules\Recipes\Models\Recipe;
use App\Modules\Recipes\Requests\RecipeStoreRequest;
use App\Modules\Recipes\Requests\RecipeUpdateRequest;
use App\Modules\Recipes\Resoueces\RecipeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Recipes",
 *     description="API Endpoints for managing recipes"
 * )
 */
class RecipeController extends Controller
{
    /**
     * Constructor
     *
     * @param RecipeService $recipeService
     */
    public function __construct(public RecipeService $recipeService)
    {
    }


    /**
     * Get a list of recipes.
     *
     * @OA\Get(
     *     path="/api/recipes",
     *     summary="Retrieve all recipes",
     *     tags={"Recipes"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of recipes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RecipeResource"))
     *     ),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * @return JsonResponse
     */
    public function index()
    {
        $workspace_id = request()->user()->workspace_id;
        $recipes = Recipe::with(['instructions', 'ingredients', 'equipments'])
            ->where('workspace_id',$workspace_id)
            ->get();
        return ApiResponse::success(RecipeResource::collection($recipes));
    }

    /**
     * @OA\Post(
     *     path="/api/recipes",
     *     summary="Create a new recipe",
     *     operationId="createRecipe",
     *     tags={"Recipes"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "category_id", "workspace_id", "status", "preparation_time", "calories", "protein", "carbs", "fats", "cover"},
     *                 @OA\Property(property="title", type="string", example="Recipe 1"),
     *                 @OA\Property(property="description", type="string", example="A delicious recipe"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="workspace_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="preparation_time", type="integer", example=30),
     *                 @OA\Property(property="calories", type="integer", example=200),
     *                 @OA\Property(property="protein", type="integer", example=15),
     *                 @OA\Property(property="carbs", type="integer", example=30),
     *                 @OA\Property(property="fats", type="integer", example=10),
     *                 @OA\Property(property="cover", type="string", format="binary"),
     *                 @OA\Property(
     *                     property="ingredients[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={5, 4}
     *                 ),
     *                 @OA\Property(
     *                     property="ingredients_quantities[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="ingredients_units[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"grams", "cups"}
     *                 ),
     *                 @OA\Property(
     *                     property="equipments[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={3, 4}
     *                 ),
     *                 @OA\Property(
     *                     property="equipments_quantities[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={"1", "2"}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_media[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     example={"erik-mclean-bGWVhFY1gH0-unsplash.jpg", "another-image.jpg"}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_timer[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={10, 5}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_descriptions[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Chop the onions", "Boil water"}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_order[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={1, 2}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Recipe created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function store(RecipeStoreRequest $request)
    {
        $recipe = DB::transaction(function () use ($request) {
            $recipe = $this->recipeService->storeRecipeMainInfo($request);
            $this->recipeService->storeRecipeIngredients($recipe, $request);
            $this->recipeService->storeRecipeEquipments($recipe, $request);
            $this->recipeService->storeRecipeInstructions($recipe, $request);
            return $recipe;
        });

        return ApiResponse::created(new RecipeResource($recipe));
    }

    /**
     * Retrieve a specific recipe.
     *
     * @OA\Get(
     *     path="/api/recipes/{id}",
     *     summary="Get a single recipe",
     *     tags={"Recipes"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the recipe",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recipe details",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeResource")
     *     ),
     *     @OA\Response(response=404, description="Recipe not found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        $recipe = Recipe::with([
            'ingredients',
            'instructions',
            'equipments',
            'category',
            'workspace'
        ])->findOrFail($id);

        return ApiResponse::success(new RecipeResource($recipe));
    }

    /**
     * Update a recipe.
     *
     * @OA\Post(
     *     path="/api/recipes/{id}",
     *     summary="Update a recipe",
     *     tags={"Recipes"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the recipe",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title", "description", "cover", "preparation_time", "category_id", "status"},
     *                 @OA\Property(property="title", type="string", example="Recipe 1"),
     *                 @OA\Property(property="description", type="string", example="A delicious recipe"),
     *                 @OA\Property(
     *                     property="cover",
     *                     type="string",
     *                     format="binary",
     *                     description="Cover image (jpeg, png, jpg, gif, svg)"
     *                 ),
     *                 @OA\Property(property="preparation_time", type="integer", example=30),
     *                 @OA\Property(property="calories", type="integer", example=200),
     *                 @OA\Property(property="protein", type="integer", example=15),
     *                 @OA\Property(property="fats", type="integer", example=10),
     *                 @OA\Property(property="carbs", type="integer", example=30),
     *                 @OA\Property(property="status", type="string", enum={"completed", "draft"}, example="completed"),
     *                 @OA\Property(property="category_id", type="integer", example=2),
     *                 @OA\Property(
     *                     property="ingredients[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="ingredients_units[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"grams", "cups", "pieces"}
     *                 ),
     *                 @OA\Property(
     *                     property="ingredients_quantities[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"100", "2", "3"}
     *                 ),
     *                 @OA\Property(
     *                     property="equipments[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={5, 6}
     *                 ),
     *                 @OA\Property(
     *                     property="equipments_quantities[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"1", "2"}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_order[]",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={1, 2, 3}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_media[]",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Instruction media files (jpeg, png, jpg, mp4)"
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_timer[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"10 min", "5 min"}
     *                 ),
     *                 @OA\Property(
     *                     property="instructions_descriptions[]",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"Chop the onions", "Boil water"}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recipe updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RecipeResource")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="Recipe not found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * @param RecipeUpdateRequest $request
     * @param Recipe $recipe
     * @return JsonResponse
     */
    public function update(RecipeUpdateRequest $request, string $id)
    {
        $newRecipe = DB::transaction(function () use ($request,$id) {
            $recipe = $this->recipeService->updateRecipeMainInfo($request, $id);
            $this->recipeService->storeRecipeIngredients($recipe, $request);
            $this->recipeService->storeRecipeEquipments($recipe, $request);
            $this->recipeService->updateRecipeInstructions($recipe, $request);
            return $recipe;
        });

        return ApiResponse::created(new RecipeResource($newRecipe));
    }

    /**
     * Delete a recipe.
     *
     * @OA\Delete(
     *     path="/api/recipes/{id}",
     *     summary="Delete a recipe",
     *     tags={"Recipes"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the recipe",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Recipe deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Recipe not found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        $recipe = Recipe::with('instructions')->findOrFail($id);

        if ($recipe->cover) {
            FileHandeler::deleteFile($recipe->cover);
        }

        if ($recipe->instructions) {
            $recipe->instructions->each(function ($instruction) {
                if ($instruction->media) {
                    FileHandeler::deleteFile($instruction->media);
                }
            });
        }

        $recipe->delete();

        return ApiResponse::message('deleted');
    }
}
