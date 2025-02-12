<?php

namespace App\Modules\RecipesComments\Requests;

use App\Http\Requests\AbstractApiRequest;


/**
 * @OA\Schema(
 *     schema="CommentnStoreRequest",
 *     type="object",
 *     required={"comment", "recipe_id"},
 *     @OA\Property(property="comment", type="string", example="comment dd "),
 *     @OA\Property(property="recipe_id", type="integer", example="1"),
 * )
 */
class CommentStoreRequest extends AbstractApiRequest
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
        return [
            'comment' => ['required', 'string'],
            'recipe_id' => ['required', 'exists:recipes,id'],
        ];
    }
}
