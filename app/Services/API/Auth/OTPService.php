<?php

namespace App\Services\API\Auth;

use App\Exceptions\CustomBusinessException;
use App\Models\Member;
use App\Notifications\API\OTPNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OTPService
{
    public function generateOTP(): string
    {
        Log::info('Generating new OTP');
        // $otp = '123456'; // For development only
        // Uncomment for production:
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return $otp;
    }

    public function isOTPValid(Member $member, string $code): bool
    {
        $isValid = $member->verification_code === $code &&
            $member->verification_code_expires_at > Carbon::now();

        return $isValid;
    }

    public function sendOTP(Member $member): void
    {

        $code = $this->generateOTP();
        $expiryMinutes = config('auth.verification_code_expiry_minutes', 10);
        $expiryTime = Carbon::now()->addMinutes($expiryMinutes);

        $member->update([
            'verification_code' => $code,
            'verification_code_expires_at' => $expiryTime,
        ]);

        // Send OTP via notification system
        try {
            $member->notify(new OTPNotification($code, $expiryMinutes));

            Log::info('OTP notification sent', [
                'member_id' => $member->id,
                'phone' => $member->full_phone,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP notification', [
                'member_id' => $member->id,
                'phone' => $member->full_phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function resendOTP(array $data): array
    {
        $member = $this->findMember($data);

        if ($this->isInCooldownPeriod($member)) {
            $timeInfo = $this->calculateRemainingTime($member);
            throw new CustomBusinessException(
                message: "Please wait {$timeInfo['formatted_time']} before requesting a new code.",
                code: 422,
                data: ['remaining_seconds' => $timeInfo['remaining_seconds']]
            );
        }

        $member->update(['otp_attempts' => 0]);
        $this->sendOTP($member);

        $member->refresh(); // Refresh to get updated verification code

        return [
            'phone' => $this->maskPhoneNumber($member->full_phone),
            'message' => 'A new verification code has been sent to your phone number.',
            'remaining_seconds' => $this->calculateRemainingSeconds($member->verification_code_expires_at),
            'formatted_time' => $this->formatTimeMessage(
                floor($this->calculateRemainingSeconds($member->verification_code_expires_at) / 60),
                $this->calculateRemainingSeconds($member->verification_code_expires_at) % 60
            ),
        ];
    }

    public function findMember(array $data): Member
    {
        $member = Member::where('full_phone', $data['full_phone'])
            ->first();

        if (! $member) {
            throw new CustomBusinessException(
                message: api('No account found with these credentials.'),
                code: 404
            );
        }

        return $member;
    }

    private function isInCooldownPeriod(Member $member): bool
    {
        return $member->verification_code_expires_at &&
            $member->verification_code_expires_at > Carbon::now()->subMinutes(1);
    }

    private function calculateRemainingTime(Member $member): array
    {
        $now = Carbon::now();
        $waitUntil = Carbon::parse($member->verification_code_expires_at);

        $remainingSeconds = $now->diffInSeconds($waitUntil);
        $remainingMinutes = floor($remainingSeconds / 60);
        $remainingSecondsModulo = $remainingSeconds % 60;

        return [
            'remaining_seconds' => $remainingSeconds,
            'formatted_time' => $this->formatTimeMessage($remainingMinutes, $remainingSecondsModulo),
        ];
    }

    public function calculateRemainingSeconds(?string $expiryTime): ?int
    {
        if (! $expiryTime) {
            return null;
        }

        $now = Carbon::now();
        $expiryAt = Carbon::parse($expiryTime);

        if ($now > $expiryAt) {
            return 0;
        }

        return $now->diffInSeconds($expiryAt);
    }

    private function formatTimeMessage(int $minutes, int $seconds): string
    {
        $timeMessage = '';

        if ($minutes > 0) {
            $timeMessage .= "{$minutes} minute".($minutes > 1 ? 's' : '');
            if ($seconds > 0) {
                $timeMessage .= ' and ';
            }
        }

        if ($seconds > 0 || $minutes === 0) {
            $timeMessage .= "{$seconds} second".($seconds > 1 ? 's' : '');
        }

        return $timeMessage;
    }

    public function maskPhoneNumber(string $phoneNumber): string
    {
        return substr($phoneNumber, 0, 2).
            str_repeat('*', strlen($phoneNumber) - 4).
            substr($phoneNumber, -2);
    }
}
