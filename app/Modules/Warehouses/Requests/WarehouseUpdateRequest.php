<?php

namespace App\Modules\Warehouses\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Warehouses\Rules\UniqueWarehouseTitleRule;
use App\Modules\Workspaces\Rules\BelongsToWorkSpaceRule;
use App\Rules\UniqueTitleRole;

/**
 * @OA\Schema(
 *     schema="WarehouseUpdateRequest",
 *     type="object",
 *     required={"title","workspace_id"},
 *     @OA\Property(property="title", type="string", example="flavors"),
 * )
 */
class WarehouseUpdateRequest extends AbstractApiRequest
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
        $id = request()->warehouse;
        $workspace_id = request()->user()->workspace_id;
        return [
            'title' => ['required', 'string', 'max:255', new UniqueWarehouseTitleRule($workspace_id, $id)],
        ];
    }
}
