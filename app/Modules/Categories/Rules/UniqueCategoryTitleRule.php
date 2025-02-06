<?php

namespace App\Modules\Categories\Rules;

use App\Modules\Categories\Category;
use App\Rules\Traits\UniqueTitleRulesTrait;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCategoryTitleRule implements ValidationRule
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
            $validation = $this->rule(Category::class, $value, $this->workspace, $this->id);
            if ($validation) {
                $fail("This category is already exist.");
            }
        } catch (\Exception $e) {
            $fail('bad format');
        }
    }
}
