<?php

namespace App\Modules\Categories\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Categories\Category;
use App\Modules\Categories\Rules\UniqueCategoryTitleRule;
use App\Modules\Workspaces\Rules\BelongsToWorkSpaceRule;


/**
 * @OA\Schema(
 *     schema="CategoryUpdateRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="title", type="string", example="Category #"),
 *     @OA\Property(property="workspace_id", type="integer", example="1"),
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
        $workspace_id = request()->workspace_id;
        return [
            'title' => ['required', 'string', 'max:255', new UniqueCategoryTitleRule($workspace_id, $id)],
            'workspace_id' => ['required', 'integer', new BelongsToWorkSpaceRule(Category::class, $workspace_id, $id)],
        ];
    }
}
