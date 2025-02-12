<?php

namespace App\Modules\Recipes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipesIngredents extends Pivot
{
    use HasFactory;

    protected $table = 'recipes_ingredents';

    protected $guarded = [];


}
