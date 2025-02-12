<?php

namespace App\Modules\Recipes\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Categories\Rules\ExistsCategoryRule;
use App\Modules\Recipes\Rules\UniqueRecipeTitleRule;


class RecipeStoreRequest extends AbstractApiRequest
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


        $equipmentsCount = count(request()->input('equipments', []));
        $ingredientsCount = count(request()->input('ingredients', []));
        $instructionsCount = count(request()->input('instructions_order', []));

        return [
            'title' => ['required', 'string', 'max:255', new UniqueRecipeTitleRule()],
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
