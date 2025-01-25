<?php

namespace App\Rules;

use Closure;
use App\Models\Section;
use App\Rules\Traits\UniqueTitleRulesTrait;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSectionTitleRule implements ValidationRule
{
    use UniqueTitleRulesTrait;

    public $id;
    public $workspace;
    
    public function __construct($workspace,$id = null)
    {
        $this->id = $id;
        $this->workspace = $workspace;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validation = $this->rule(Section::class, $value, $this->workspace, $this->id);
        if ($validation) {
            $fail("This section is already exist.");
        }
    }
}
