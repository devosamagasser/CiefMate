<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trait\BelongsToWorkspaceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
