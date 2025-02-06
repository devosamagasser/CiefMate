<?php

namespace App\Modules\Sections;

use App\Models\Trait\BelongsToWorkspaceTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory,BelongsToWorkspaceTrait;

    protected $fillable = ['title', 'workspace_id'];

    protected $hidden = ['workspace_id','created_at','updated_at'];

}
