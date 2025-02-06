<?php

namespace App\Modules\Auth\Requests;


use App\Http\Requests\AbstractApiRequest;
use App\Modules\Users\User;

class LoginRequest extends AbstractApiRequest
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
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->where('verified',false)->exists()) {
                        $fail('Email is not verified.');
                    }
                },
            ],
            'password' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'password.required' => 'Password is required',
            'password.string' => 'Password is invalid',
        ];
    }
}
