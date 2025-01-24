<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\AbstractApiRequest;
use App\Models\Category;
use App\Rules\UniqueTitleRole;


/**
 * @OA\Schema(
 *     schema="CategoryUpdateRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="name", type="string", example="Category #"),
 * )
 */
class CategoryUpdateRequest extends AbstractApiRequest
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
        $id = request()->category;
        return [
            'title' => ['required', 'string', 'max:255', new UniqueTitleRole(Category::class,$id)],
        ];
    }
}
