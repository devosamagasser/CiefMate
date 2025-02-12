<?php

namespace App\Modules\Auth;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\EmailVerificationRequest;
use App\Modules\Users\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/otp-verification",
     *     summary="Verify OTP for email verification",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EmailVerificationRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Verified successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="This code is invalid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function otpVerification(EmailVerificationRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $otp = (new Otp())->validate($request->email, $request->code);

            if (!$otp->status) {
                return ApiResponse::validationError([
                    'code' => 'This code is invalid'
                ]);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $user->verified = true;
            $user->save();

            DB::table('otps')->where('identifier', $request->email)->delete();

            return ApiResponse::message('Verified successfully.');
        });
    }

    /**
     * @OA\Post(
     *     path="/api/resend-otp",
     *     summary="Resend OTP for email verification",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP re-sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP re-sent successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email already verified.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function reSendOtp(Request $request)
    {
        $validation = Validator::make(
            $request->only('email'),
            [
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) {
                        if (User::where('email', $value)->where('verified', true)->exists()) {
                            $fail('Email already verified.');
                        }
                    },
                ]
            ]
        );

        if ($validation->fails()) {
            return ApiResponse::validationError($validation->errors()->first());
        }

        $data = $validation->validated();

        DB::table('otps')->where('identifier', $data['email'])->delete();

        $user = User::where('email', $data['email'])->firstOrFail();
        $user->notify(new EmailVerificationNotification());

        return ApiResponse::message('OTP re-sent successfully.');
    }
}
