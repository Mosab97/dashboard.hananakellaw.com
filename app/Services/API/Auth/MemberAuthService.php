<?php

namespace App\Services\API\Auth;

use App\Enums\MemberAccountType;
use App\Exceptions\CustomBusinessException;
use App\Http\Resources\API\MemberResource;
use App\Models\Member;
use App\Models\SchoolProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MemberAuthService
{
    public function login(array $credentials)
    {
        try {
            $member = Member::with(['schoolProfile'])->select('id', 'full_phone', 'active', 'is_verified')->where('full_phone', $credentials['full_phone'])->first();
            if (! $member) {
                throw new CustomBusinessException(
                    message: api('phone not found. Please register first.'),
                    code: 401
                );
            }

            // no need to use password check here
            // if (!Hash::check($credentials['password'], $member->password)) {
            //     Log::warning('Login failed: Invalid password', ['member_id' => $member->id]);
            //     throw new CustomBusinessException(
            //         message: api('Incorrect password. Please try again.'),
            //         code: 401
            //     );
            // }

            // For school accounts, check subscription
            if ($member->isSchool()) {
                $this->validateSchoolSubscription($member);
            }

            // Check if member is active
            if (! $member->active) {
                throw new CustomBusinessException(
                    message: api('Your account is currently inactive. Please contact support.'),
                    code: 403,
                    data: [
                        'is_subscribed' => $member->hasActiveSubscription(),
                        'is_verified' => $member->is_verified,
                        'is_active' => $member->active,
                    ]
                );
            }

            // Check if member is verified
            if (! $member->is_verified) {
                throw new CustomBusinessException(
                    message: api('Please verify your account first.'),
                    code: 403,
                    data: [
                        'is_subscribed' => $member->hasActiveSubscription(),
                        'is_verified' => $member->is_verified,
                        'is_active' => $member->active,
                    ]
                );
            }

            // we send an opt verification code to the user instead using whatsapp
            (new OTPService)->sendOTP($member);

            return [
                'phone' => (new OTPService)->maskPhoneNumber($member->full_phone),
                'expires_in' => config('auth.verification_code_expiry_minutes', 10),
            ];
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function logout($user): array
    {
        $user->tokens()->delete();

        return ['message' => 'Logout successful'];
    }

    public function verifyLoginOtp(array $data): MemberResource
    {

        try {
            // Find member
            $member = Member::where('full_phone', $data['full_phone'])->first();

            if (! $member) {
                throw new CustomBusinessException(
                    message: api('No account found with this phone number.'),
                    code: 404
                );
            }

            // Validate OTP
            if (! (new OTPService)->isOTPValid($member, $data['otp'])) {
                // Increment failed attempts
                $member->increment('otp_attempts');

                throw new CustomBusinessException(
                    message: api('Invalid or expired OTP code.'),
                    code: 422
                );
            }

            // Clear OTP data after successful verification
            $memberData = [
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'otp_attempts' => 0,
                'last_login_at' => Carbon::now(),

            ];
            if (isset($data['fcm_token'])) {
                $memberData['fcm_token'] = $data['fcm_token'];
            }
            $member->update($memberData);

            // $member->update(['last_login_at' => Carbon::now()]);

            // $member['access_token'] = $member->createToken('auth_token')->plainTextToken;

            // if ($member->isSchool()) {
            //     Log::info('Loading school profile', ['member_id' => $member->id]);
            //     $member->load([
            //         'schoolProfile', //:id,member_id,time_start,time_end,location_description,lat,lng',
            //         'schoolProfile.workingDays:id,name,constant_name'
            //     ]);
            // } else if ($member->isTeacher()) {
            //     Log::info('Loading teacher profile', ['member_id' => $member->id]);
            //     $member->load([
            //         'teacherProfile', //:id,member_id,location_description,lat,lng',
            //     ]);

            //     // Check if teacher has already attended today
            //     Log::info('Checking teacher attendance for today', ['member_id' => $member->id]);
            //     $today = Carbon::today();
            //     $todayAttendance = $member->teacherProfile->attendanceRecords()
            //         ->whereDate('attendance_date_time', $today)
            //         ->first();

            //     $member['has_attended_today'] = $todayAttendance ? true : false;
            //     $member['today_attendance'] = $todayAttendance;

            //     Log::info('Teacher attendance check complete', [
            //         'member_id' => $member->id,
            //         'has_attended_today' => $member['has_attended_today']
            //     ]);
            // }

            return $this->loginMember($member);
        } catch (\Exception $e) {
            Log::error('Login OTP verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function LoginMember($member): MemberResource
    {
        $member['access_token'] = $member->createToken('auth_token')->plainTextToken;

        if ($member->isSchool()) {
            Log::info('Loading school profile', ['member_id' => $member->id]);
            $member->load([
                'schoolProfile', // :id,member_id,time_start,time_end,location_description,lat,lng',
                'schoolProfile.workingDays:id,name,constant_name',
                'type:id,name,constant_name',
            ]);
        } elseif ($member->isTeacher()) {
            Log::info('Loading teacher profile', ['member_id' => $member->id]);
            $member->load([
                'teacherProfile', // :id,member_id,location_description,lat,lng',
                'teacherProfile.classrooms',
                'type:id,name,constant_name',
            ]);

            // Check if teacher has already attended today
            Log::info('Checking teacher attendance for today', ['member_id' => $member->id]);
            $today = Carbon::today();
            $todayAttendance = $member->teacherProfile->attendanceRecords()
                ->whereDate('attendance_date_time', $today)
                ->first();

            $member['has_attended_today'] = $todayAttendance ? true : false;
            $member['today_attendance'] = $todayAttendance;

            Log::info('Teacher attendance check complete', [
                'member_id' => $member->id,
                'has_attended_today' => $member['has_attended_today'],
            ]);
        }

        return new MemberResource($member);
    }

    protected function validateSchoolSubscription(Member $member): void
    {
        $schoolProfile = $member->schoolProfile;

        if (! $schoolProfile) {
            throw new CustomBusinessException(
                message: api('School profile not found. Please complete your profile setup.'),
                code: 404
            );
        }
        if (! $member->hasActiveSubscription()) {
            throw new CustomBusinessException(
                message: api('Your subscription has expired. Please renew your subscription to continue.'),
                code: 403,
                data: [
                    'is_subscribed' => false,
                    'is_verified' => $member->is_verified,
                    'is_active' => $member->active,
                ]
            );
        }
    }

    public function register(array $data): array
    {

        try {
            DB::beginTransaction();

            // Prepare member data
            $memberData = [
                'name' => $data['member_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone'],
                'country_code' => $data['country_code'],
                'password' => Hash::make($data['password']),
                'type_id' => MemberAccountType::SCHOOL->getFromDatabase()->id,
                'active' => false, // Requires activation
                'is_verified' => false, // Requires phone verification
                'full_phone' => $data['full_phone'],
                'preferred_language' => app()->getLocale(),
            ];

            // Create member
            $member = Member::create($memberData);

            // Prepare school profile data
            $schoolData = [
                'member_id' => $member->id,
                'name' => $data['school_name'],
                'whatsapp' => $member->full_phone, // Set WhatsApp number same as phone
            ];

            // Create school profile
            $school = $member->schoolProfile;

            if (! $school) {
                // Fallback if the observer doesn't fire for some reason
                $schoolData['member_id'] = $member->id;
                $school = SchoolProfile::create($schoolData);
            } else {
                // Update the school profile created by the observer with our data
                $school->update($schoolData);
            }

            // Send OTP for verification
            $otpService = new OTPService;
            $otpService->sendOTP($member);

            DB::commit();

            return [
                'phone' => $otpService->maskPhoneNumber($member->full_phone),
                'expires_in' => config('auth.verification_code_expiry_minutes', 10),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function verifyPhone(array $data): MemberResource
    {

        try {
            // Find member

            $member = Member::where('full_phone', $data['full_phone'])->first();

            if (! $member) {
                throw new CustomBusinessException(
                    message: api('No account found with this phone number.'),
                    code: 404
                );
            }

            // Check if member is already verified
            if ($member->is_verified) {
                throw new CustomBusinessException(
                    message: api('Your account is already verified.'),
                    code: 422
                );
            }

            // Validate OTP
            if (! (new OTPService)->isOTPValid($member, $data['otp'])) {
                $member->increment('otp_attempts');
                throw new CustomBusinessException(
                    message: api('Invalid or expired OTP code.'),
                    code: 422
                );
            }

            // Update member status
            $upatedData = [
                'verification_code' => null,
                'verification_code_expires_at' => null,
                'otp_attempts' => 0,
                'is_verified' => true,
                'active' => false,

            ];
            if (isset($data['fcm_token'])) {
                $upatedData['fcm_token'] = $data['fcm_token'];
            }
            $member->update($upatedData);

            $member->refresh();
            $member->load(['schoolProfile']);

            return $this->LoginMember($member);
        } catch (\Exception $e) {
            Log::error('Phone verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
