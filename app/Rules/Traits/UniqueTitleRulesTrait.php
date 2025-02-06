<?php
namespace App\Rules\Traits;

Trait UniqueTitleRulesTrait
{
    public function rule($model, $title, $workspace_id, $id)
    {
        try{
            if ($id) {
                $modelWorkspaceId = $model::where('id',$id)->firstOrFail()->workspace_id;
                if($workspace_id != $modelWorkspaceId) {
                    return false;
                }
            }
            $query = $model::userWorkspace($workspace_id)->where('title', $title);

            if($id) {
                $query = $query->where('id', '<>', $id);
            }
            return $query->exists();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
