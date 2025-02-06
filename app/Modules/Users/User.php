<?php

namespace App\Modules\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Trait\BelongsToWorkspaceTrait;
use App\Modules\Sections\Section;
use App\Modules\Workspaces\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, BelongsToWorkspaceTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'provider',
        'provider_id',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function scopeSectionFilter(Builder $builder)
    {
        $section_id = request()->section_id ?? null;
        $builder->when($section_id,function ($builder,$value){
            $builder->where('section_id',$value);
        });
    }
}
