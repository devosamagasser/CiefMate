<?php

namespace App\Modules\Members\Request;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Sections\Rules\ExistsSectionRule;
use App\Modules\Workspaces\Rules\ExistsWorkSpaceRule;

/**
 * @OA\Schema(
 *     schema="MemberUpdateRequest",
 *     type="object",
 *     required={"rule", "section_id", "workspace_id"},
 *     @OA\Property(property="rule", type="string", example="Chef, Assistant Chef, Trainee"),
 *     @OA\Property(property="section_id", type="integer", example="1"),
 *     @OA\Property(property="workspace_id", type="integer", example="1"),
 * )
 */
class MemberUpdateRequest extends AbstractApiRequest
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
        $workspaceId = request('workspace_id');
        return [
            'workspace_id' => ['required', 'integer', new ExistsWorkSpaceRule()],
            'section_id' => ['required', 'integer', new ExistsSectionRule($workspaceId)],
            'rule' => ['required', 'string', 'in:Chef,Assistant Chef,Trainee'],
        ];
    }
}
