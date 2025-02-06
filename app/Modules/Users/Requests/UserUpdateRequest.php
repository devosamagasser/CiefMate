<?php

namespace App\Modules\Users\Requests;

use App\Http\Requests\AbstractApiRequest;

class UserUpdateRequest extends AbstractApiRequest
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
        $id = request()->user()->id;
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255','unique:users,email,'.$id],
            'phone' => ['nullable', 'string', 'max:255','unique:users,phone,'.$id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
