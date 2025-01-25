<?php 

namespace App\Models\Trait;

use App\Models\Workspace;

trait BelongsToWorkspaceTrait 
{
    public function scopeUserWorkspace($query,$workspace = null)
    {
        return $query->whereHas('workspace',function($query)use($workspace){
            $query->where('user_id',request()->user()->id);
            $query->when($workspace, function($query,$value){
                $query->where('id',$value);
            });
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}