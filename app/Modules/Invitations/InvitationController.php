<?php

namespace App\Modules\Invitations;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Invitations\Request\InviteMembersRequest;
use App\Modules\Users\User;
use App\Notifications\SendInvitation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/invitation",
     *     summary="Invite a new workspace",
     *     tags={"Invitations"},
     *     security={{"Bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InviteMembersRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Member invited successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="The invite has been sent")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email is not valid."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=451,
     *         description="Unavailable for legal reasons",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=451),
     *             @OA\Property(property="message", type="string", example="This user isn\'t free to join your workspace")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *         )
     *     )
     * )
     */
    public function invite(InviteMembersRequest $request)
    {
        try {
            $member = User::where('email', $request->email)->firstOrFail();

            if ($member->rules !== 'Guest') {
                return ApiResponse::faild(null, 'This user isn\'t free to join your workspace', \Illuminate\Http\Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
            }

            $invitation = Invitation::create([
                'user_id' => $member->id,
                'workspace_id' => request()->user()->workspace_id,
                'section_id' => $request->section_id,
                'rules' => $request->rule,
            ]);

            $member->notify(new SendInvitation($invitation->id));

            return ApiResponse::message('The invite has been sent');

        } catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('This user doesn\'t exist');
        } catch (\Exception $e) {
            return ApiResponse::serverError();
        }
    }


    /**
     * @OA\get(
     *     path="/api/invitation/{id}/accept",
     *     summary="Accept Invite",
     *     tags={"Invitations"},
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the invitation",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitation accepted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Invitation accepted, you are now one of the teem"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invitation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Invitation not found"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *         )
     *     )
     * )
     */
    public function accept($id)
    {
        try {
            $invitation = Invitation::where('id', $id)->firstOrFail();
            DB::beginTransaction();
            $this->invitationProcess($invitation);
            DB::commit();
            return ApiResponse::message('Congrats, you are now one of the teem! 🥰');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return ApiResponse::notFound('Invitation not found');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::serverError($e->getMessage());
        }
    }

    private function invitationProcess($invitation)
    {
        try {
            $user = User::findOrFail($invitation->user_id);
            $user->update([
                'workspace_id' => $invitation->workspace_id,
                'section_id' => $invitation->section_id,
                'rules' => $invitation->rules,
            ]);
            $invitation->delete();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
