<?php
namespace App\Http\Controllers\Traits;

use App\Facades\FileHandeler;


Trait ControllerTraits
{
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


    protected function updateWith($attr, $old, $path, $request, $exten = 'jpg')
    {
        try{
            $data = $this->updatedDataFormated($request, $request->except($attr));
            $data[$attr] = FileHandeler::updateFile($request->$attr, $old, $path, $exten);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateWithFile($fileAttrName, $request, $model, $pathStoring, $filExt = 'jpg')
    {
        if ($request->has($fileAttrName)) {
            return $this->updateWith($fileAttrName, $model->$fileAttrName, $pathStoring, $request, $filExt);
        }
        return $this->updatedDataFormated($request);
    }





}
