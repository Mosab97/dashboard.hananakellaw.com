<?php

namespace App\Services\API\Auth;

use App\Exceptions\CustomBusinessException;
use App\Http\Resources\API\MemberResource;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordManagerService
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function forgotPassword(array $data): MemberResource
    {
        $result = $this->otpService->resendOTP($data);

        return $result;
    }

    public function verifyResetOTP(array $data): MemberResource
    {
        Log::info('Starting OTP verification process', [
            'email' => $data['email'],
        ]);

        try {
            $member = $this->otpService->findMember($data);

            Log::info('Member found for OTP verification', [
                'member_id' => $member->id,
                'current_attempts' => $member->otp_attempts,
            ]);

            // Check for maximum attempts
            if ($member->otp_attempts >= 3) {
                Log::warning('Maximum OTP attempts exceeded', [
                    'member_id' => $member->id,
                    'attempts' => $member->otp_attempts,
                ]);

                throw new CustomBusinessException(
                    message: api('Too many attempts. Please request a new verification code.'),
                    code: 422,
                    data: [
                        'attempts_remaining' => 0,
                        'remaining_seconds' => $this->otpService->calculateRemainingSeconds($member->verification_code_expires_at),
                    ]
                );
            }

            if (! $this->otpService->isOTPValid($member, $data['verification_code'])) {
                // Increment attempts counter
                $member->increment('otp_attempts');
                $remainingAttempts = 3 - $member->otp_attempts;

                Log::warning('Invalid OTP provided', [
                    'member_id' => $member->id,
                    'attempt_time' => now(),
                    'attempts_made' => $member->otp_attempts,
                    'attempts_remaining' => $remainingAttempts,
                ]);

                throw new CustomBusinessException(
                    message: api('The verification code is invalid.'),
                    code: 422,
                    data: [
                        'attempts_remaining' => $remainingAttempts,
                        'remaining_seconds' => $this->otpService->calculateRemainingSeconds($member->verification_code_expires_at),
                    ]
                );
            }

            Log::info('OTP validated successfully, generating reset token', [
                'member_id' => $member->id,
            ]);

            // Reset attempts counter on successful verification
            $member->update(['otp_attempts' => 0]);

            $resetToken = $member->createToken('password_reset')->plainTextToken;
            $cleanToken = explode('|', $resetToken)[1];

            Log::info('Reset token generated successfully', [
                'member_id' => $member->id,
                'token_length' => strlen($cleanToken),
            ]);

            $member->reset_token = $cleanToken;

            Log::info('Reset token process completed', [
                'member_id' => $member->id,
                'process_completion_time' => now(),
            ]);

            return new MemberResource($member);
        } catch (Exception $e) {
            Log::error('Error in OTP verification process', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function resetPassword(array $data): MemberResource
    {
        Log::info('Starting password reset process', [
            'email' => $data['email'],
        ]);

        try {
            $member = $this->otpService->findMember($data);

            Log::info('member found for password reset', [
                'member_id' => $member->id,
            ]);

            $validToken = $member->tokens()
                ->where('name', 'password_reset')
                ->where('token', hash('sha256', $data['reset_token']))
                ->exists();

            if (! $validToken) {
                Log::warning('Invalid reset token attempt', [
                    'member_id' => $member->id,
                    'attempt_time' => now(),
                ]);

                throw new CustomBusinessException(
                    message: 'Invalid reset token.',
                    code: 401
                );
            }

            Log::info('Valid reset token confirmed, proceeding with password update', [
                'member_id' => $member->id,
            ]);

            // Update member data
            $member->update([
                'password' => Hash::make($data['password']),
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'otp_attempts' => 0,
            ]);

            Log::info('Password updated successfully', [
                'member_id' => $member->id,
            ]);

            // Clean up tokens
            $member->tokens()->delete();
            Log::info('Old tokens cleared', [
                'member_id' => $member->id,
            ]);

            // Generate new access token
            $member['access_token'] = $member->createToken('auth_token')->plainTextToken;
            Log::info('New access token generated', [
                'member_id' => $member->id,
                'token_type' => 'auth_token',
            ]);

            Log::info('Password reset process completed successfully', [
                'member_id' => $member->id,
                'process_completion_time' => now(),
            ]);

            return new MemberResource($member);
        } catch (Exception $e) {
            Log::error('Error in password reset process', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_trace' => $e->getTraceAsString(),
                'error_time' => now(),
            ]);
            throw $e;
        }
    }
}
