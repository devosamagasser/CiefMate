<?php

namespace App\Modules\Equipments\Rules;

use App\Modules\Equipments\Equipment;
use App\Rules\Traits;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEquipmentNameRule implements ValidationRule
{
    use Traits\UniqueNameRulesTrait;

    public $id;
    public $workspace_id;

    public function __construct($workspace_id, $id = null)
    {
        $this->id = $id;
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
            $validation = $this->rule(Equipment::class, $value, $this->id, $this->workspace_id);
            if ($validation) {
                $fail("The equipment is already exists.");
            }
        } catch (\Exception $e) {
            $fail('bad format');
        }
    }
}
