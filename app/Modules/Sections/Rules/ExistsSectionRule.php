<?php

namespace App\Modules\Sections\Rules;

use App\Modules\Sections\Section;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExistsSectionRule implements ValidationRule
{

    public $workspace_id;

    public function __construct($workspace_id)
    {
        $this->workspace_id = $workspace_id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $validation = Section::userWorkspace($this->workspace_id)->where('id', $value)->exists();
            if (!$validation) {
                $fail("The selected section is invalid.");
            }
        } catch (\Exception $e) {
            $fail("The selected section is invalid.");
        }

    }
}
