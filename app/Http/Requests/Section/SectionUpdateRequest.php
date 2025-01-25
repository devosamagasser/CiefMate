<?php

namespace App\Http\Requests\Section;

use App\Models\Section;
use App\Rules\BelongsToWorkSpaceRule;
use App\Rules\UniqueSectionTitleRule;
use App\Http\Requests\AbstractApiRequest;

/**
 * @OA\Schema(
 *     schema="SectionUpdateRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="title", type="string", example="section 1"),
 *     @OA\Property(property="workspace_id", type="string", example="1", description="ID of the workspace")
 * )
 */
class SectionUpdateRequest extends AbstractApiRequest
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
        $id = request()->section;
        $workspace_id = request()->workspace_id; 
        return [
            'title' => ['required', 'string', 'max:255', new UniqueSectionTitleRule($workspace_id, $id)],
            'workspace_id' => ['required', 'integer', new BelongsToWorkSpaceRule(Section::class, $workspace_id, $id)],
        ];
    }
}
