<?php

namespace App\Modules\Ingredients;

use App\Models\Trait\BelongsToWarehouseTrait;
use App\Models\Trait\BelongsToWorkspaceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredent extends Model
{
    use HasFactory, BelongsToWorkspaceTrait, BelongsToWarehouseTrait;

    protected $fillable = [
        'name',
        'cover',
        'description',
        'unit',
        'quantity',
        'warehouse_id',
        'workspace_id'
    ];

    protected $hidden = ['warehouse_id', 'workspace_id', 'created_at', 'updated_at'];

}
