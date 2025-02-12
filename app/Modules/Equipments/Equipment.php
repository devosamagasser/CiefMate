<?php

namespace App\Modules\Equipments;

use App\Models\Trait\BelongsToWarehouseTrait;
use App\Models\Trait\BelongsToWorkspaceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory, BelongsToWorkspaceTrait, BelongsToWarehouseTrait;

    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'cover',
        'description',
        'quantity',
        'warehouse_id',
        'workspace_id'
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
