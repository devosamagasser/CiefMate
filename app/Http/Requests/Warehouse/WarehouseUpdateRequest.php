<?php

namespace App\Http\Requests\Warehouse;

use App\Models\Warehouse;
use App\Rules\UniqueTitleRole;
use App\Http\Requests\AbstractApiRequest;

/**
 * @OA\Schema(
 *     schema="WarehouseUpdateRequest",
 *     type="object",
 *     required={"title"},
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
        return [
            'title' => ['required', 'string', 'max:255', new UniqueTitleRole(Warehouse::class,$id)],
        ];
    }
}
