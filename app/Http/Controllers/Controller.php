<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function updatedDataFormated($request,$data = null)
    {
        $data = $data ?? $request->all();
        $updatedData = [] ;
        foreach ($data as $key => $datum) {
            if($request->filled($key))
                $updatedData[$key] = $datum;
        }
        return $updatedData;
    }
}
