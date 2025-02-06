<?php

namespace App\Modules\Warehouses;

use App\Models\Trait\BelongsToWorkspaceTrait;
use App\Modules\Equipments\Equipment;
use App\Modules\Ingredients\Ingredent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory, BelongsToWorkspaceTrait;

    protected $fillable = ['title', 'workspace_id', 'type'];

    protected $hidden = ['updated_at','created_at','workspace_id'];

    public function ingredents()
    {
        return $this->hasMany(Ingredent::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

}
