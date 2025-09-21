<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CustomBusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ResetOtpRequest;
use App\Http\Requests\API\VerifyLoginOtpRequest;
use App\Services\API\Auth\MemberAuthService;
use App\Services\API\Auth\OTPService;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthOtpController extends Controller
{
    protected $authService;

    protected $otpService;

    public function __construct(
        MemberAuthService $authService,
        OTPService $otpService,
    ) {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }

    public function verifyLoginOtp(VerifyLoginOtpRequest $request)
    {
        try {

            $result = $this->authService->verifyLoginOtp($request->validated());

            return apiSuccess(
                $result,
                api('Login successful')
            );
        } catch (CustomBusinessException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Verify Login OTP failed', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => $request->all(),
            ]);

            return apiError(
                null,
                api('Verify Login OTP failed'),
                500
            );
        }
    }

    public function verifyPhone(VerifyLoginOtpRequest $request)
    {
        try {
            $result = $this->authService->verifyPhone($request->validated());

            return apiSuccess(
                $result,
                api('Phone verification successful')
            );
        } catch (CustomBusinessException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Verify Phone failed', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => $request->all(),
            ]);

            return apiError(
                api('Verify Phone failed'),
                500
            );
        }
    }

    public function resendOtp(ResetOtpRequest $request)
    {
        try {
            $result = $this->otpService->resendOtp(['full_phone' => $request->validated()['full_phone']]);

            return apiSuccess(
                $result,
                api('OTP Resent successfully')
            );
        } catch (CustomBusinessException $e) {
            throw $e;
        } catch (Exception $e) {
            return apiError(
                api('Resend OTP failed'),
                500
            );
        }
    }
}
