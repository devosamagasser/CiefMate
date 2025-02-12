<?php

namespace App\Modules\Recipes\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Categories\Rules\ExistsCategoryRule;
use App\Modules\Recipes\Rules\UniqueRecipeTitleRule;

/**
 * @OA\Schema(
 *     schema="RecipeUpdateRequest",
 *     type="object",
 *     required={"title", "workspace_id", "description", "cover", "preparation_time", "category_id", "status"},
 *     @OA\Property(property="title", type="string", example="Recipe 1"),
 *     @OA\Property(property="workspace_id", type="integer", example=1, description="ID of the workspace"),
 *     @OA\Property(property="description", type="string", example="A delicious recipe"),
 *     @OA\Property(property="cover", type="string", format="binary", description="Image file for the recipe (jpeg, png, jpg, gif, svg)"),
 *     @OA\Property(property="preparation_time", type="integer", example=30, description="Preparation time in minutes (must be at least 1)"),
 *     @OA\Property(property="calories", type="integer", example=200, description="Calories per serving (optional, must be at least 1)"),
 *     @OA\Property(property="protein", type="integer", example=15, description="Protein content (optional, must be at least 1)"),
 *     @OA\Property(property="fats", type="integer", example=10, description="Fat content (optional, must be at least 1)"),
 *     @OA\Property(property="carbs", type="integer", example=30, description="Carbohydrate content (optional, must be at least 1)"),
 *     @OA\Property(property="status", type="string", enum={"completed", "draft"}, example="completed", description="Recipe status (must be either 'completed' or 'draft')"),
 *     @OA\Property(property="category_id", type="integer", example=2, description="ID of the associated category (must exist in the database)"),
 *
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={1, 2, 3},
 *         description="List of ingredient IDs (optional, must exist in the database)"
 *     ),
 *     @OA\Property(
 *         property="ingredients_units",
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"grams", "cups", "pieces"},
 *         description="Measurement units for each ingredient (must match ingredient count)"
 *     ),
 *     @OA\Property(
 *         property="ingredients_quantities",
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"100", "2", "3"},
 *         description="Quantities for each ingredient (must match ingredient count)"
 *     ),
 *
 *     @OA\Property(
 *         property="equipments",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={5, 6},
 *         description="List of equipment IDs (optional, must exist in the database)"
 *     ),
 *     @OA\Property(
 *         property="equipments_quantities",
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"1", "2"},
 *         description="Quantities for each equipment item (must match equipment count)"
 *     ),
 *
 *     @OA\Property(
 *         property="instructions_order",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         example={1, 2, 3},
 *         description="Order of instructions (must be unique, required if instructions exist)"
 *     ),
 *     @OA\Property(
 *         property="instructions_media",
 *         type="array",
 *         @OA\Items(type="string", format="binary"),
 *         example={"instruction1.jpg", "step2.mp4"},
 *         description="Instruction media files (jpeg, png, jpg for images, mp4 for videos)"
 *     ),
 *     @OA\Property(
 *         property="instructions_timer",
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"10 min", "5 min"},
 *         description="Timer for each instruction step (must match instructions count)"
 *     ),
 *     @OA\Property(
 *         property="instructions_descriptions",
 *         type="array",
 *         @OA\Items(type="string"),
 *         example={"Chop the onions", "Boil water"},
 *         description="Descriptions for each instruction step (must match instructions count)"
 *     )
 * )
 */
class RecipeUpdateRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workspace_id = request()->user()->workspace_id;
        $ingredientsCount = count(request()->ingredients ?? []);
        $equipmentsCount = count(request()->equipments ?? []);
        $instructionsCount = count(request()->instructions_order ?? []);
        $id = request()->query('recipe');

        return [
            'title' => ['required', 'string', 'max:255', new UniqueRecipeTitleRule($id)],
            'description' => 'required|string',
            'cover' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg',
            'preparation_time' => 'required|integer|min:1',
            'calories' => 'nullable|integer|min:1',
            'protein' => 'nullable|integer|min:1',
            'fats' => 'nullable|integer|min:1',
            'carbs' => 'nullable|integer|min:1',
            'status' => 'required|string|in:completed,draft',
            'category_id' => ['required', 'integer', new ExistsCategoryRule($workspace_id)],

            'ingredients' => 'required|array',
            'ingredients.*' => 'required|integer|exists:ingredents,id',

            'ingredients_units' => ['required', 'array', 'size:' . $ingredientsCount],
            'ingredients_units.*' => 'required|string',

            'ingredients_quantities' => ['required', 'array', 'size:' . $ingredientsCount],
            'ingredients_quantities.*' => 'required|string',


            'equipments' => 'required|array',
            'equipments.*' => 'required|integer|exists:equipments,id',
            'equipments_quantities' => ['required', 'array', 'size:' . $equipmentsCount],
            'equipments_quantities.*' => 'required|string',

            'instructions_order' => ['required', 'array', 'distinct', 'min:1'],
            'instructions_order.*' => 'required|integer',

            'instructions_media' => 'nullable|array',
            'instructions_media.*' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg,video/mp4',

            'instructions_timer' => ['required', 'array', 'size:' . $instructionsCount],
            'instructions_timer.*' => 'required|string',

            'instructions_descriptions' => ['required', 'array', 'size:' . $instructionsCount],
            'instructions_descriptions.*' => 'required|string',
        ];
    }
}
