<?php

namespace App\Rules;

use App\Models\Workspace;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueWorkSpaceNameRule implements ValidationRule
{

    use Traits\UniqueNameRulesTrait;

    public $id;
    
    public function __construct($id = null)
    {
        $this->id = $id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validation = $this->rule(Workspace::class, $value, $this->id);
        if ($validation) {
            $fail("The workspace is already exists.");
        }
    }
}
