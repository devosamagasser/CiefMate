<?php 

namespace App\Models\Trait;

use App\Models\Workspace;

trait BelongsToWorkspaceTrait 
{
    public function scopeUserWorkspace($query)
    {
        return $query->whereHas('workspace',function($query){
            $query->where('user_id',request()->user()->id);
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}