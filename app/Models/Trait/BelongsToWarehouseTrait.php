<?php

namespace App\Models\Trait;

use App\Modules\Warehouses\Warehouse;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToWarehouseTrait
{
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
