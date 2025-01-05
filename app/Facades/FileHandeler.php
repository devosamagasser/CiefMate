<?php

namespace App\Facades;

use App\Http\Controllers\Facades\FileHandelerController;
use Illuminate\Support\Facades\Facade;

class FileHandeler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FileHandelerController::class;
    }
}
