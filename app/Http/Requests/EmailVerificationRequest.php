<?php

namespace App\Http\Requests;

use App\Modules\Users\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="EmailVerificationRequest",
 *     type="object",
 *     required={"email", "code"},
 *     @OA\Property(property="email", type="string", format="email", example="example@gmail.com"),
 *     @OA\Property(property="code", type="string", example="129912")
 * )
 */
class EmailVerificationRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'exists:users,email',
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->where('verified',true)->exists()) {
                        $fail('Email already verified.');
                    }
                },
            ],
            'code' => ['required', 'string', 'size:6' ],
        ];
    }
}
