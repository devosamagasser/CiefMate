<?php

namespace App\Http\Requests\Section;

use App\Rules\UniqueTitleRole;
use App\Rules\ExistsWorkSpaceRole;
use App\Http\Requests\AbstractApiRequest;
use App\Models\Section;


/**
 * @OA\Schema(
 *     schema="SectionStoreRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="title", type="string", example="Section #"),
 *     @OA\Property(property="workspace_id", type="string", example="1", description="ID of the workspace")
 * )
 */
class SectionStoreRequest extends AbstractApiRequest
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
            'title' => ['required', 'string', 'max:255', new UniqueTitleRole(Section::class)],
            'workspace_id' => ['required', 'integer', new ExistsWorkSpaceRole()],
        ];
    }
}
