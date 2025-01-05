<?php

namespace App\Facades;

use App\Http\Controllers\Facades\ApiResponseController;
use Illuminate\Support\Facades\Facade;

class ApiResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ApiResponseController::class;
    }
}
