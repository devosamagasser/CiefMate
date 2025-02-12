<?php

namespace App\Modules\Members;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ControllerTraits;
use App\Modules\Members\Request\MemberUpdateRequest;
use App\Modules\Users\User;
use App\Modules\Users\UserResources;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MemberController extends Controller
{
    use ControllerTraits;
    /**
     * @OA\Get(
     *     path="/api/member",
     *     summary="Get all members of workspace by ID of workspace",
     *     tags={"Members"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=false,
     *         description="filter members by setcion section",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of members retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
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
            $workspace_id = request()->user()->workspace_id;
            $members = User::with('section','workspace')
                ->sectionFilter()
                ->where('workspace_id',$workspace_id)
                ->get();
            return ApiResponse::success(UserResources::collection($members));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('no members found');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/member/{id}",
     *     summary="Get a specific member by ID",
     *     tags={"Members"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the member",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member not found"),
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
        $member = User::userWorkspace()->where('id', $id)->firstOrFail();
        return ApiResponse::success(new UserResources($member));
    }

    /**
     * @OA\Put(
     *     path="/api/member/{id}",
     *     summary="Update a member",
     *     tags={"Members"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the member",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/MemberUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Memeber info.. updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Memeber's info.. updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *     response=422,
     *     description="Validation Error",
     *     @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", example=422),
     *         @OA\Property(property="message", type="string", example="Validation Error"),
     *         @OA\Property(property="errors", type="object", example={"rule": {"The rule is not valid."}})
     *     )
     * ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member not found"),
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
    public function update(MemberUpdateRequest $request, $id)
    {
        try {
            $member = User::userWorkspace($request->user()->workspace_id)->where('id', $id)->firstOrFail();
            $data = $this->updatedDataFormated($request,$request->only(['section_id','rule']));
            $member->fill($data);
            if($member->isDirty()){
                $member->save();
                return ApiResponse::updated(new UserResources($member));
            }
            return ApiResponse::message('no changes made');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Member not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/member/{id}/fire",
     *     summary="Fire a member",
     *     tags={"Members"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the member",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member fired successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member has\'t been one of the teem."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member not found"),
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
    public function fire($id)
    {
        try {
            $user = User::userWorkspace()->where('id', $id)->firstOrFail();
            $user->update([
                'section_id' => null,
                'workspace_id' => null,
                'rule' => 'Guest'
            ]);
            return ApiResponse::message('Member has\'t been one of the teem.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Member not found');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }

}
