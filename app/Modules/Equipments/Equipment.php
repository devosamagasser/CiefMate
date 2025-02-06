<?php

namespace App\Modules\Equipments;

use App\Models\Trait\BelongsToWorkspaceTrait;
use App\Modules\Warehouses\Requests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory, BelongsToWorkspaceTrait;

    protected $fillable = [
        'name',
        'cover',
        'description',
        'quantity',
        'warehous_id',
        'workspace_id'
    ];

    protected $hidden = ['warehouse_id', 'created_at', 'updated_at'];

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
    }}
