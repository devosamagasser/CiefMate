<?php

namespace App\Http\Controllers\Facades;

use Illuminate\Support\Facades\Storage;

class FileHandelerController
{
    /**
     * @param $file
     * @return string
     */
    public function storeFile($file,$path)
    {
        $name = time().$file->getClientOriginalName();
        Storage::putFileAs($path, $file, $name);
        return $name;
    }

    /**
     * @param $file
     * @param string $oldname
     * @return string
     */
    public function updateFile($file,string $oldname,$path,$name)
    {
        $this->deleteFile($oldname);
        return $this->storeFile($file,$path,$name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteFile(string $name)
    {
        Storage::delete($name);
    }


}
