<?php

namespace App\Modules\Categories\Rules;

use App\Modules\Categories\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsCategoryRule implements ValidationRule
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
            $validation = Category::userWorkspace($this->workspace_id)->where('id', $value)->exists();
            if (!$validation) {
                $fail("The selected Category is invalid.");
            }
        } catch (\Exception $e) {
            $fail("The selected Category is invalid.");
        }

    }
}
