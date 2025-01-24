<?php

namespace App\Http\Requests\Section;

use App\Http\Requests\AbstractApiRequest;
use App\Models\Section;
use App\Rules\UniqueTitleRole;

/**
 * @OA\Schema(
 *     schema="SectionUpdateRequest",
 *     type="object",
 *     required={"title"},
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
        return [
            'title' => ['required', 'string', 'max:255', new UniqueTitleRole(Section::class,$id)],
        ];
    }
}
