<?php

namespace App\Modules\Workspaces;

use App\Models\Color;
use App\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color_id', 'user_id'];

    public function color()
    {
        return $this->BelongsTo(Color::class);
    }

    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    public function ScopeUserWorkspaces($query)
    {
        $user_id = request()->user()->id;
        return $query->where('user_id', $user_id);
    }
}
