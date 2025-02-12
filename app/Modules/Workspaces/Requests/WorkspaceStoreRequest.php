<?php

namespace App\Modules\Workspaces\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Workspaces\Rules\UniqueWorkSpaceNameRule;

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
            'name' => ['required','string','unique:workspaces,name'],
            'color_id' => 'string|exists:colors,id'
        ];
    }
}
