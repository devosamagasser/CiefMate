<?php

namespace App\Modules\Ingredients;

use App\Models\Trait\BelongsToWarehouseTrait;
use App\Models\Trait\BelongsToWorkspaceTrait;
use App\Modules\Warehouses\Requests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredent extends Model
{
    use HasFactory, BelongsToWorkspaceTrait;

    protected $fillable = [
        'name',
        'cover',
        'description',
        'unit',
        'quantity',
        'warehous_id',
        'workspace_id'
    ];

    protected $hidden = ['warehouse_id', 'workspace_id', 'created_at', 'updated_at'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeWarehouseFilter(Builder $builder)
    {
        $warehouse_id = request()->warehouse_id ?? null;
        $builder->when($warehouse_id,function ($builder,$value){
            $builder->where('warehouse_id',$value);
        });
    }

}
