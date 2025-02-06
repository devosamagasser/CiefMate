<?php

namespace App\Modules\Equipments;

use App\Facades\ApiResponse;
use App\Facades\FileHandeler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerTraits;
use App\Modules\Equipments\Requests\EquipmentStoreRequest;
use App\Modules\Equipments\Requests\EquipmentUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EquipmentController extends Controller
{

    use ControllerTraits;

   /**
     * @OA\Get(
     *     path="/api/workspaces/{id}/equipments",
     *     summary="Get a equipments of workspace by ID of workspace",
     *     tags={"Equipments"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the workspace",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=false,
     *         description="type of equipments",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of equipments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Equipments")
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
    public function index($workspace_id)
    {
        try {
            $equipments = Equipment::with(['warehouse','workspace'])
                ->userWorkspace($workspace_id)
                ->WarehouseFilter()->get();
                return ApiResponse::success(EquipmentsResources::collection($equipments));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/equipments",
     *     summary="Create a new equipment",
     *     tags={"Equipments"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentStoreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Equipment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Equipment created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Equipments"
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
    public function store(EquipmentStoreRequest $request)
    {
        try {
            if($request->has('cover')){
                $cover = FileHandeler::storeFile($request->cover, 'equipments', 'jpg');
                $request->merge(['cover' => $cover]);
            }
            $equipment = Equipment::create($request->all());
            FileHandeler::storeFile($equipment);
            return ApiResponse::created(new EquipmentsResources($equipment));
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }


   /**
     * @OA\Get(
     *     path="/api/equipment/{id}",
     *     summary="Get a equipment by ID",
     *     tags={"Equipments"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="equipment retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Equipments"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Equipment not found"),
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
            $equipment = Equipment::userWorkspace()->where('id', $id)->firstOrFail();
            return ApiResponse::success(new EquipmentsResources($equipment));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Equipment not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }

   /**
     * @OA\Put(
     *     path="/api/equipment/{id}",
     *     summary="Update a equipment",
     *     tags={"Equipments"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EquipmentUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Equipment updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Equipments"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Equipment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Equipment not found"),
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
    public function update(EquipmentUpdateRequest $request, $id)
    {
        try {
            $equipment = Equipment::userWorkspace()->where('id', $id)->firstOrFail();
            $data = $this->updateWithFile('cover', $request, $equipment, 'equipments');
            $equipment->fill($data);
            if($equipment->isDirty()){
                $equipment->save();
                return ApiResponse::updated(new EquipmentsResources($equipment));
            }
            return ApiResponse::message('no changes made');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('equipment not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }




/**
     * @OA\Delete(
     *     path="/api/equipment/{id}",
     *     summary="Delete a equipment",
     *     tags={"Equipments"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the equipment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="equipment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="equipment deleted successfully"),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="equipment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="equipment not found"),
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
            Equipment::userWorkspace()->where('id', $id)->firstOrFail()->delete();
            return ApiResponse::message('equipment deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('equipment not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }
}
