<?php 
namespace App\Rules\Traits;

Trait UniqueNameRulesTrait 
{
    public function rule($model, $name, $id, $workspace_id = null)
    {
        try{
            $modeorkspace_id = $model::where('id',$id)->firstOrFail()->workspace_id;
            if($workspace_id != $modeorkspace_id) {
                return false;
            }
            $query = $model::userWorkspaces($workspace_id)->where('name', $name);
            
            if($id) {
                $query = $query->where('id', '<>', $id);
            }
            return $query->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}