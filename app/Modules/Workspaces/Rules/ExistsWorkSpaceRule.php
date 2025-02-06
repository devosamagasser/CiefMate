<?php

namespace App\Modules\Workspaces\Rules;

use App\Modules\Workspaces\Workspace;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsWorkSpaceRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $validation = Workspace::userWorkspaces()->where('id', $value)->exists();
            if (!$validation) {
                $fail("The selected workspace is invalid.");
            }
        } catch (\Exception $e) {
            $fail('bad format');
        }

    }
}
