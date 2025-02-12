<?php

namespace App\Modules\Recipes\Models;

use App\Models\Trait\BelongsToWorkspaceTrait;
use App\Modules\Categories\Category;
use App\Modules\Equipments\Equipment;
use App\Modules\Ingredients\Ingredent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory, BelongsToWorkspaceTrait;

    protected $fillable = [
        'title',
        'description',
        'cover',
        'preparation_time',
        'calories',
        'protein',
        'fats',
        'carbs',
        'status',
        'workspace_id',
        'category_id',
    ];

    protected static function booted()
    {
        static::creating(function ($recipe) {
            if (request()->user()) {
                $recipe->workspace_id = request()->user()->workspace_id;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'recipe_equipments', 'recipe_id', 'equipment_id')->withPivot(['quantity']);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredent::class, 'recipe_ingredents', 'recipe_id', 'ingredent_id')->withPivot(['quantity', 'unit']);
    }


    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }
}
