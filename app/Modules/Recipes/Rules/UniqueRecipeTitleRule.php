<?php

namespace App\Modules\Recipes\Rules;

use App\Modules\Recipes\Models\Recipe;
use App\Rules\Traits\UniqueTitleRulesTrait;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueRecipeTitleRule implements ValidationRule
{
    use UniqueTitleRulesTrait;

    public $id;
    public $workspace;

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->workspace = request()->user()->workspace;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $validation = $this->rule(Recipe::class, $value, $this->workspace, $this->id);
            if ($validation) {
                $fail("This recipe is already exist.");
            }
        } catch (\Exception $e) {
            $fail("bad request.");
        }
    }
}
