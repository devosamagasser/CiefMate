<?php

namespace App\Rules;

use App\Models\Ingredent;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueIngredientNameRule implements ValidationRule
{
    use Traits\UniqueNameRulesTrait;
    public $id;
    public $workspace_id;
    
    public function __construct($workspace_id, $id = null)
    {
        $this->id = $id;
        $this->$workspace_id = $workspace_id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $validation = $this->rule(Ingredent::class, $value, $this->id, $this->workspace_id);
        if ($validation) {
            $fail("The ingredient is already exists.");
        }
    }
}
