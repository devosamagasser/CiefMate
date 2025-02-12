<?php

namespace App\Modules\Recipes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public $timestamps = false;
}
