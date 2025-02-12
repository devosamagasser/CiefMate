<?php

namespace App\Modules\Sections\Requests;

use App\Http\Requests\AbstractApiRequest;
use App\Modules\Sections\Rules\UniqueSectionTitleRule;

/**
 * @OA\Schema(
 *     schema="SectionUpdateRequest",
 *     type="object",
 *     required={"title", "workspace_id"},
 *     @OA\Property(property="title", type="string", example="section 1"),
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
        $workspace_id = request()->user()->workspace_id;
        return [
            'title' => ['required', 'string', 'max:255', new UniqueSectionTitleRule($workspace_id, $id)],
        ];
    }
}
