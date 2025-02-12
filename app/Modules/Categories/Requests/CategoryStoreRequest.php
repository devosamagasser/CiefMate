<?php

namespace App\Modules\Categories\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Categories\Rules\UniqueCategoryTitleRule;
use App\Modules\Workspaces\Rules\ExistsWorkSpaceRule;

/**
 * @OA\Schema(
 *     schema="CategoryStoreRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="title", type="string", example="category 1"),
 * )
 */
class CategoryStoreRequest extends AbstractApiRequest
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
        return [
            'title' => ['required', 'string', 'max:255', new UniqueCategoryTitleRule($workspace_id)],
        ];
    }
}
