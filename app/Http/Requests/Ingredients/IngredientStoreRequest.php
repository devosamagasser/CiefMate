<?php

namespace App\Http\Requests\Ingredients;

use App\Rules\ExistsWarehouseRule;
use App\Rules\ExistsWorkSpaceRole;
use App\Rules\UniqueIngredientNameRule;
use App\Http\Requests\AbstractApiRequest;

/**
 * @OA\Schema(
 *     schema="IngredientStoreRequest",
 *     type="object",
 *     required={"name", "unit", "quantity", "workspace_id"},
 *     @OA\Property(property="name", type="string", example="shugar"),
 *     @OA\Property(property="cover", type="binary", example="image.jpg"),
 *     @OA\Property(property="description", type="string", example="some decription"),
 *     @OA\Property(property="unit", type="string", example="l, ml, g, unit, kg"),
 *     @OA\Property(property="quantity", type="integer", example="11, 12, 13"),
 *     @OA\Property(property="warehouse_id", type="string", example="1", description="ID of the warehouse"),
 *     @OA\Property(property="workspace_id", type="string", example="1", description="ID of the workspace")
 * )
 */
class IngredientStoreRequest extends AbstractApiRequest
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
        $workspace_id = request()->workspace_id;
        return [
            'warehous_id' => ['nullable', 'integer', new ExistsWarehouseRule()],
            'workspace_id' => ['required', 'integer', new ExistsWorkspaceRole()],
            'name' => ['required', 'string', 'max:255',new UniqueIngredientNameRule($workspace_id)],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'string'],
        ];

    }
}
