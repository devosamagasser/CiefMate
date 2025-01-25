<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BelongsToWorkSpaceRule implements ValidationRule
{

    public $id;
    public $workspace;
    public $model;
    
    public function __construct($model, $workspace, $id = null)
    {
        $this->id = $id;
        $this->workspace = $workspace;
        $this->model = $model;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validation = $this->model::where('id',$this->id)->where('workspace_id', $value)->exists();
        if (!$validation) {
            $fail("The selected workspace is invalid.");
        }
    }
}