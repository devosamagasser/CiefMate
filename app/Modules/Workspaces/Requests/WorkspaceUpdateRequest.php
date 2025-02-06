<?php

namespace App\Modules\Workspaces\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Workspaces\Rules\UniqueWorkSpaceNameRule;

/**
 * @OA\Schema(
 *     schema="WorkspaceUpdateRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Updated Workspace Name"),
 *     @OA\Property(property="color_id", type="string", example="1", description="ID of the color")
 * )
 */
class WorkspaceUpdateRequest extends AbstractApiRequest
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
        $id = request('workspace');
        return [
            'name' => ['string',new UniqueWorkSpaceNameRule($id)],
            'color' => 'string|exists:colors,id'
        ];
    }
}
