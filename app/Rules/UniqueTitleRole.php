<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueTitleRole implements ValidationRule
{

    public $id;
    public $model;
    
    public function __construct($model,$id = null)
    {
        $this->model = $model;
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = $this->model::userWorkspace()->where('title', $value);
        if($this->id) {
            $query = $query->where('id', '<>', $this->id);
        }
            
        $validation = $query->exists();
        if ($validation) {
            $class = strtolower(class_basename($this->model));
            $fail("This $class is already exist.");
        }
    }
}
