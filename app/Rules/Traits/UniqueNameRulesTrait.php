<?php
namespace App\Rules\Traits;

Trait UniqueNameRulesTrait
{
    public function rule($model, $name, $id = null, $workspace_id = null)
    {
        try{
            if ($id) {
                $modelWorkspaceId = $model::where('id',$id)->firstOrFail()->workspace_id;
                if($workspace_id != $modelWorkspaceId) {
                    return false;
                }
            }

            $query = $model::userWorkspace($workspace_id)->where('name', $name);

            if($id) {
                $query = $query->where('id', '<>', $id);
            }
            return $query->exists();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
