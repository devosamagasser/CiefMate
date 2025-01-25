<?php

namespace App\Http\Requests\workspace;

use App\Rules\UniqueWorkSpaceNameRule;
use App\Http\Requests\AbstractApiRequest;

/**
 * @OA\Schema(
 *     schema="WorkspaceStoreRequest",
 *     type="object",
 *     required={"name", "color_id"},
 *     @OA\Property(property="name", type="string", example="My Workspace"),
 *     @OA\Property(property="color_id", type="string", example="1", description="ID of the color")
 * )
 */
class WorkspaceStoreRequest extends AbstractApiRequest
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
            'name' => ['string',new UniqueWorkSpaceNameRule()],
            'color' => 'string|exists:colors,id'
        ];
    }
}