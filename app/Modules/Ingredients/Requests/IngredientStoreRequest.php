<?php

namespace App\Modules\Ingredients\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Ingredients\Rules\UniqueIngredientNameRule;
use App\Modules\Warehouses\Rules\ExistsWarehouseRule;
use App\Modules\Workspaces\Rules\ExistsWorkSpaceRule;

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
        $workspace_id = request()->user()->workspace_id;
        return [
            'warehouse_id' => ['nullable', 'integer', new ExistsWarehouseRule()],
            'name' => ['required', 'string', 'max:255',new UniqueIngredientNameRule($workspace_id)],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'string'],
        ];

    }
}
