<?php

namespace App\Modules\Sections\Rules;

use App\Modules\Sections\Section;
use App\Rules\Traits\UniqueTitleRulesTrait;
use Closure;
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
        try {
            $validation = $this->rule(Section::class, $value, $this->workspace, $this->id);
            if ($validation) {
                $fail("This section is already exist.");
            }
        } catch (\Exception $e) {
            $fail("bad request.");
        }
    }
}
