<?php

namespace App\Modules\Invitations\Request;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Sections\Rules\ExistsSectionRule;
use App\Modules\Workspaces\Rules\ExistsWorkSpaceRule;

/**
 * @OA\Schema(
 *     schema="InviteMembersRequest",
 *     type="object",
 *     required={"email", "rule", "section_id", "workspace_id"},
 *     @OA\Property(property="email", type="string", example="example@example.com"),
 *     @OA\Property(property="rule", type="string", example="Chef, Assistant Chef, Trainee"),
 *     @OA\Property(property="section_id", type="integer", example="1"),
 * )
 */
class InviteMembersRequest extends AbstractApiRequest
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
            'email' => ['required', 'string', 'email', 'max:255','exists:users,email'],
            'section_id' => ['required', 'integer', new ExistsSectionRule($workspace_id)],
            'rule' => ['required', 'string', 'in:Chef,Assistant Chef,Trainee'],
        ];
    }
}
