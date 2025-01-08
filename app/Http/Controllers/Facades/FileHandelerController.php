<?php

namespace App\Http\Controllers\Facades;

use Illuminate\Support\Facades\Storage;

class FileHandelerController
{
    /**
     * @param $file
     * @return string
     */
    public function storeFile($file,$path,$name = null)
    {
        try{
            $newName = $name ?? time()."avatar.jpg";
            Storage::disk('public')->putFileAs($path, $file, $newName);
            return $newName;
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param string $oldname
     * @return string
     */
    public function updateFile($file,string $oldname,$path,$name)
    {
        try{
            $this->deleteFile($oldname);
            return $this->storeFile($file,$path,$name);
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteFile(string $name)
    {
        try {
            return Storage::delete($name);
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }


}
