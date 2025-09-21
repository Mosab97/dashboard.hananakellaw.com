<?php

namespace App\Services\API\Notifications;

use App\Models\AttendanceTracker;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Notifications\API\AbsenceNotification;
use App\Notifications\API\InfoCompletionNotification;
use App\Notifications\API\TardinessNotification;
use Illuminate\Support\Facades\Log;

class GuardianNotificationService
{
    /**
     * Send tardiness notification to a student's guardian or a teacher
     *
     * @param  AttendanceTracker  $attendanceRecord  The tardiness record
     * @param  string  $preferredLanguage  'en' or 'ar' for language preference
     * @return array Result of the notification attempt
     */
    public function sendTardinessNotification(AttendanceTracker $attendanceRecord, string $preferredLanguage = 'en'): array
    {
        Log::info('Starting tardiness notification process', [
            'record_id' => $attendanceRecord->id,
            'attendable_type' => $attendanceRecord->attendable_type,
            'language' => $preferredLanguage,
        ]);

        // Make sure this is a tardiness record
        if (! $attendanceRecord->isTardiness()) {
            Log::warning('Attempted to send tardiness notification for non-tardiness record', [
                'record_id' => $attendanceRecord->id,
                'status' => $attendanceRecord->status_id,
            ]);

            return [
                'success' => false,
                'error' => 'Record is not a tardiness record',
            ];
        }

        // Get the attendable model (student or teacher)
        $attendable = $attendanceRecord->attendable;

        if (! $attendable) {
            Log::error('Attendable not found for tardiness notification', [
                'attendable_id' => $attendanceRecord->attendable_id,
                'attendable_type' => $attendanceRecord->attendable_type,
            ]);

            return [
                'success' => false,
                'error' => 'Attendable not found',
            ];
        }

        // Determine recipient based on attendable type
        $recipient = null;
        $recipientType = null;

        if ($attendanceRecord->attendable_type === Student::class) {
            // For students, notify their guardian
            $recipient = $attendable->guardian;
            $recipientType = 'guardian';

            if (! $recipient) {
                Log::warning('Guardian not found for student', [
                    'student_id' => $attendable->id,
                    'student_name' => $attendable->name,
                ]);

                return [
                    'success' => false,
                    'error' => 'Guardian not found for student',
                ];
            }
        } elseif ($attendanceRecord->attendable_type === TeacherProfile::class) {
            // For teachers, notify the teacher directly via their member account
            $recipient = $attendable->member;
            $recipientType = 'teacher';

            if (! $recipient) {
                Log::warning('Member account not found for teacher', [
                    'teacher_id' => $attendable->id,
                    'teacher_name' => $attendable->name,
                ]);

                return [
                    'success' => false,
                    'error' => 'Member account not found for teacher',
                ];
            }
        } else {
            // Unsupported attendable type
            Log::warning('Unsupported attendable type for tardiness notification', [
                'type' => $attendanceRecord->attendable_type,
            ]);

            return [
                'success' => false,
                'error' => 'Unsupported attendable type',
            ];
        }

        // Ensure recipient has phone number for WhatsApp
        if (empty($recipient->phone_number) && empty($recipient->mobile) && empty($recipient->full_phone)) {
            Log::warning("$recipientType has no phone number for WhatsApp", [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
            ]);

            return [
                'success' => false,
                'error' => "$recipientType phone number not available for WhatsApp",
            ];
        }

        // Set the preferred language for the recipient if available
        if (property_exists($recipient, 'locale') && ! empty($recipient->locale)) {
            $preferredLanguage = $recipient->locale;
            Log::info('Using recipient\'s preferred language', ['language' => $preferredLanguage]);
        } elseif (property_exists($recipient, 'preferred_language') && ! empty($recipient->preferred_language)) {
            $preferredLanguage = $recipient->preferred_language;
            Log::info('Using recipient\'s preferred language', ['language' => $preferredLanguage]);
        }

        try {
            // Log notification attempt
            Log::info('Sending tardiness notification to '.$recipientType, [
                'attendable_id' => $attendable->id,
                'recipient_id' => $recipient->id,
                'phone' => $recipient->phone_number ?? $recipient->mobile ?? $recipient->full_phone,
                'minutes_late' => $attendanceRecord->total_minutes_late,
            ]);

            // Create and send the notification
            $recipient->notify(new TardinessNotification($attendanceRecord, $preferredLanguage));

            // Log success
            Log::info('Successfully sent tardiness notification', [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
            ]);

            return [
                'success' => true,
                'message' => 'Tardiness notification sent successfully',
            ];
        } catch (\Exception $e) {
            // Log error
            Log::error('Failed to send tardiness notification', [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send notification: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send absence notification to a student's guardian or a teacher
     *
     * @param  AttendanceTracker  $attendanceRecord  The absence record
     * @param  string  $preferredLanguage  'en' or 'ar' for language preference
     * @return array Result of the notification attempt
     */
    public function sendAbsenceNotification(AttendanceTracker $attendanceRecord, string $preferredLanguage = 'en'): array
    {
        Log::info('Starting absence notification process', [
            'record_id' => $attendanceRecord->id,
            'attendable_type' => $attendanceRecord->attendable_type,
            'language' => $preferredLanguage,
        ]);

        // Make sure this is an absence record
        if (! $attendanceRecord->isAbsent()) {
            Log::warning('Attempted to send absence notification for non-absence record', [
                'record_id' => $attendanceRecord->id,
                'status' => $attendanceRecord->status_id,
            ]);

            return [
                'success' => false,
                'error' => 'Record is not an absence record',
            ];
        }

        // Get the attendable model (student or teacher)
        $attendable = $attendanceRecord->attendable;

        if (! $attendable) {
            Log::error('Attendable not found for absence notification', [
                'attendable_id' => $attendanceRecord->attendable_id,
                'attendable_type' => $attendanceRecord->attendable_type,
            ]);

            return [
                'success' => false,
                'error' => 'Attendable not found',
            ];
        }

        // Determine recipient based on attendable type
        $recipient = null;
        $recipientType = null;

        if ($attendanceRecord->attendable_type === Student::class) {
            // For students, notify their guardian
            $recipient = $attendable->guardian;
            $recipientType = 'guardian';

            if (! $recipient) {
                Log::warning('Guardian not found for student', [
                    'student_id' => $attendable->id,
                    'student_name' => $attendable->name,
                ]);

                return [
                    'success' => false,
                    'error' => 'Guardian not found for student',
                ];
            }
        } elseif ($attendanceRecord->attendable_type === TeacherProfile::class) {
            // For teachers, notify the teacher directly via their member account
            $recipient = $attendable->member;
            $recipientType = 'teacher';

            if (! $recipient) {
                Log::warning('Member account not found for teacher', [
                    'teacher_id' => $attendable->id,
                    'teacher_name' => $attendable->name,
                ]);

                return [
                    'success' => false,
                    'error' => 'Member account not found for teacher',
                ];
            }
        } else {
            // Unsupported attendable type
            Log::warning('Unsupported attendable type for absence notification', [
                'type' => $attendanceRecord->attendable_type,
            ]);

            return [
                'success' => false,
                'error' => 'Unsupported attendable type',
            ];
        }

        // Ensure recipient has phone number for WhatsApp
        if (empty($recipient->phone_number) && empty($recipient->mobile) && empty($recipient->full_phone)) {
            Log::warning("$recipientType has no phone number for WhatsApp", [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
            ]);

            return [
                'success' => false,
                'error' => "$recipientType phone number not available for WhatsApp",
            ];
        }

        // Set the preferred language for the recipient if available
        if (property_exists($recipient, 'locale') && ! empty($recipient->locale)) {
            $preferredLanguage = $recipient->locale;
            Log::info('Using recipient\'s preferred language', ['language' => $preferredLanguage]);
        } elseif (property_exists($recipient, 'preferred_language') && ! empty($recipient->preferred_language)) {
            $preferredLanguage = $recipient->preferred_language;
            Log::info('Using recipient\'s preferred language', ['language' => $preferredLanguage]);
        }

        try {
            // Log notification attempt
            Log::info('Sending absence notification to '.$recipientType, [
                'attendable_id' => $attendable->id,
                'recipient_id' => $recipient->id,
                'phone' => $recipient->phone_number ?? $recipient->mobile ?? $recipient->full_phone,
            ]);

            // Create and send the notification
            $recipient->notify(new AbsenceNotification($attendanceRecord, $preferredLanguage));

            // Log success
            Log::info('Successfully sent absence notification', [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
            ]);

            return [
                'success' => true,
                'message' => 'Absence notification sent successfully',
            ];
        } catch (\Exception $e) {
            // Log error
            Log::error('Failed to send absence notification', [
                'recipient_id' => $recipient->id,
                'attendable_id' => $attendable->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send notification: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send information completion request to a guardian
     *
     * @param  Student  $student  The student whose information needs to be completed
     * @param  string  $preferredLanguage  'en' or 'ar' for language preference
     * @return array Result of the notification attempt
     */
    public function sendInfoCompletionRequest(Student $student, string $preferredLanguage = 'en'): array
    {
        Log::info('Starting information completion request process', [
            'student_id' => $student->id,
            'language' => $preferredLanguage,
        ]);

        // Get the guardian for this student
        $guardian = $student->guardian;

        if (! $guardian) {
            Log::warning('Guardian not found for student', [
                'student_id' => $student->id,
                'student_name' => $student->name,
            ]);

            return [
                'success' => false,
                'error' => 'Guardian not found for student',
            ];
        }

        // Ensure guardian has phone number for WhatsApp
        if (empty($guardian->phone_number) && empty($guardian->mobile) && empty($guardian->full_phone)) {
            Log::warning('Guardian has no phone number for WhatsApp', [
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
            ]);

            return [
                'success' => false,
                'error' => 'Guardian phone number not available for WhatsApp',
            ];
        }

        // Set the preferred language for the guardian if available
        if (property_exists($guardian, 'locale') && ! empty($guardian->locale)) {
            $preferredLanguage = $guardian->locale;
            Log::info('Using guardian\'s preferred language', ['language' => $preferredLanguage]);
        }

        try {
            // Log notification attempt
            Log::info('Sending information completion request to guardian', [
                'student_id' => $student->id,
                'guardian_id' => $guardian->id,
                'phone' => $guardian->phone_number ?? $guardian->mobile ?? $guardian->full_phone,
            ]);

            // Create and send the notification
            $guardian->notify(new InfoCompletionNotification($student, $preferredLanguage));

            // Log success
            Log::info('Successfully sent information completion request', [
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
            ]);

            return [
                'success' => true,
                'message' => 'Information completion request sent successfully',
            ];
        } catch (\Exception $e) {
            // Log error
            Log::error('Failed to send information completion request', [
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send notification: '.$e->getMessage(),
            ];
        }
    }
}
