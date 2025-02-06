<?php

namespace App\Modules\Workspaces;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerTraits;
use App\Modules\Workspaces\Requests\WorkspaceStoreRequest;
use App\Modules\Workspaces\Requests\WorkspaceUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Workspaces",
 *     description="Endpoints for managing workspaces"
 * )
 */
class WorkspaceController extends Controller
{
    use ControllerTraits;
    /**
     * @OA\Get(
     *     path="/api/workspaces",
     *     summary="Get all workspaces",
     *     tags={"Workspaces"},
     *     security={{"Bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of workspaces retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Workspace")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $workspaces = Workspace::userWorkspaces()->get();
            return ApiResponse::success(WorkspacesResource::collection($workspaces));
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }

    /**
     * @OA\Post(
     *     path="/api/workspaces",
     *     summary="Create a new workspace",
     *     tags={"Workspaces"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Workspace created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Workspace created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Workspace"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function store(WorkspaceStoreRequest $request)
    {
        try {
            $user = $request->user();
            $request->merge(['user_id' => $user->id]);
            DB::beginTransaction();
            $workspace = Workspace::create($request->all());
            if($user->rules == 'Guest'){
                $user->rules = 'Owner';
                $user->save();
            }
            DB::commit();
            return ApiResponse::created(new WorkspacesResource($workspace));
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/workspaces/{id}",
     *     summary="Get a workspace by ID",
     *     tags={"Workspaces"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workspace retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Workspace"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Workspace not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Workspace not found"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $workspace = Workspace::userWorkspaces()->where('id', $id)->firstOrFail();
            return ApiResponse::success(new WorkspacesResource($workspace));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Workspace not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }


    /**
     * @OA\Put(
     *     path="/api/workspaces/{id}",
     *     summary="Update a workspace",
     *     tags={"Workspaces"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WorkspaceUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workspace updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Workspace"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Workspace not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Workspace not found"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function update(WorkspaceUpdateRequest $request, $id)
    {
        try {
            $workspace = Workspace::userWorkspaces()->where('id', $id)->firstOrFail();
            $data = $this->updatedDataFormated($request);
            $workspace->fill($data);
            if($workspace->isDirty()){
                $workspace->save();
                return ApiResponse::updated(new WorkspacesResource($workspace));
            }
            return ApiResponse::message('no changes made');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Workspace not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/workspaces/{id}",
     *     summary="Delete a workspace",
     *     tags={"Workspaces"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Workspace deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Workspace deleted successfully"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Workspace not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Workspace not found"),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            Workspace::userWorkspaces()->where('id', $id)->firstOrFail()->delete();
            return ApiResponse::message('Workspace deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Workspace not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }


}
