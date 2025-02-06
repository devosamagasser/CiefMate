<?php

namespace App\Modules\Warehouses\Rules;

use App\Modules\Warehouses\Warehouse;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsWarehouseRule implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $validation = Warehouse::userWorkspace()->where('id', $value)->exists();
            if (!$validation) {
                $fail("The selected workspace is invalid.");
            }
        } catch (\Exception $e) {
            $fail('bad format');
        }

    }
}
