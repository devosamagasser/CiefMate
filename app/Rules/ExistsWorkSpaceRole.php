<?php

namespace App\Rules;

use App\Models\Workspace;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsWorkSpaceRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validation = Workspace::userWorkspaces()->where('id', $value)->exists();
        if (!$validation) {
            $fail("The selected workspace is invalid.");
        }
    }
}
