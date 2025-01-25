<?php 
namespace App\Rules\Traits;

Trait UniqueTitleRulesTrait 
{
    public function rule($model, $title, $workspace_id, $id)
    {
        try{ 
            $modeorkspace_id = $model::where('id',$id)->firstOrFail()->workspace_id;
            if($workspace_id != $modeorkspace_id) {
                return false;
            }
            $query = $model::userWorkspace($workspace_id)->where('title', $title);
            
            if($id) {
                $query = $query->where('id', '<>', $id);
            }
            return $query->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}