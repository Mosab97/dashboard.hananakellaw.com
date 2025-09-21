<?php

namespace Database\Seeders;

use App\Enums\MemberAccountType;
use App\Models\Member;
use App\Notifications\AppNotification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    protected array $notificationTypes = [
        'general', 'announcement', 'attendance', 'absence', 'tardiness',
        'behavior', 'performance', 'meeting', 'exam', 'holiday',
    ];

    protected array $notificationTitles = [
        'general' => [
            'en' => 'General Information',
            'ar' => 'معلومات عامة',
        ],
        'announcement' => [
            'en' => 'Important Announcement',
            'ar' => 'إعلان مهم',
        ],
        'attendance' => [
            'en' => 'Attendance Update',
            'ar' => 'تحديث الحضور',
        ],
        'absence' => [
            'en' => 'Absence Notification',
            'ar' => 'إشعار غياب',
        ],
        'tardiness' => [
            'en' => 'Tardiness Report',
            'ar' => 'تقرير التأخر',
        ],
        'behavior' => [
            'en' => 'Behavior Update',
            'ar' => 'تحديث السلوك',
        ],
        'performance' => [
            'en' => 'Performance Report',
            'ar' => 'تقرير الأداء',
        ],
        'meeting' => [
            'en' => 'Meeting Invitation',
            'ar' => 'دعوة اجتماع',
        ],
        'exam' => [
            'en' => 'Exam Schedule',
            'ar' => 'جدول الامتحانات',
        ],
        'holiday' => [
            'en' => 'Holiday Announcement',
            'ar' => 'إعلان العطلة',
        ],
    ];

    protected array $notificationContents = [
        'general' => [
            'en' => 'This is a general information notification from the school administration.',
            'ar' => 'هذا إشعار معلومات عامة من إدارة المدرسة.',
        ],
        'announcement' => [
            'en' => 'Please be informed that the school will be holding a special event next week.',
            'ar' => 'يرجى العلم أن المدرسة ستقيم حدثًا خاصًا الأسبوع المقبل.',
        ],
        'attendance' => [
            'en' => 'The attendance record has been updated for this month.',
            'ar' => 'تم تحديث سجل الحضور لهذا الشهر.',
        ],
        'absence' => [
            'en' => 'There has been an absence recorded. Please check the details.',
            'ar' => 'تم تسجيل غياب. يرجى التحقق من التفاصيل.',
        ],
        'tardiness' => [
            'en' => 'A tardiness incident has been recorded. Please review the details.',
            'ar' => 'تم تسجيل حادثة تأخر. يرجى مراجعة التفاصيل.',
        ],
        'behavior' => [
            'en' => 'A new behavior report has been added to the student\'s record.',
            'ar' => 'تمت إضافة تقرير سلوك جديد إلى سجل الطالب.',
        ],
        'performance' => [
            'en' => 'The latest performance assessment is now available for review.',
            'ar' => 'أصبح تقييم الأداء الأخير متاحًا الآن للمراجعة.',
        ],
        'meeting' => [
            'en' => 'You are invited to attend a meeting at the school.',
            'ar' => 'أنت مدعو لحضور اجتماع في المدرسة.',
        ],
        'exam' => [
            'en' => 'The exam schedule for the upcoming term has been published.',
            'ar' => 'تم نشر جدول الامتحانات للفصل الدراسي القادم.',
        ],
        'holiday' => [
            'en' => 'The school will be closed for the upcoming holiday.',
            'ar' => 'ستكون المدرسة مغلقة للعطلة القادمة.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean existing notifications for a fresh start
        DB::table('notifications')->truncate();

        // Get all members with profiles
        $members = Member::whereHas('teacherProfile')
            ->orWhereHas('schoolProfile')
            ->get();

        if ($members->isEmpty()) {
            $this->command->info('No members with profiles found. Please seed members first.');

            return;
        }

        $this->command->info('Seeding notifications for '.$members->count().' members...');

        // Create notifications for each member
        foreach ($members as $member) {
            $this->createNotificationsForMember($member);
        }

        $this->command->info('Notifications seeded successfully.');
    }

    /**
     * Create random notifications for a member
     */
    protected function createNotificationsForMember(Member $member): void
    {
        // Create between 5-15 notifications per member
        $count = rand(5, 15);

        for ($i = 0; $i < $count; $i++) {
            $this->createRandomNotification($member);
        }
    }

    /**
     * Create a random notification
     */
    protected function createRandomNotification(Member $member): void
    {
        // Pick a random notification type
        $type = Arr::random($this->notificationTypes);

        // Create base notification data
        $data = [
            'title' => $this->notificationTitles[$type],
            'content' => $this->notificationContents[$type],
            'type' => $type,
            'sender_id' => $this->getRandomSenderId(),
            'related_id' => rand(1, 100),
            'metadata' => $this->getMetadataForType($type),
            'timestamp' => now()->timestamp,
        ];

        // Randomly decide if notification should be read or not (70% unread, 30% read)
        $readAt = (rand(1, 10) <= 3) ? now()->subHours(rand(1, 72)) : null;

        // Create a notification record directly for better control over timestamps
        $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));

        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'type' => AppNotification::class,
            'notifiable_type' => Member::class,
            'notifiable_id' => $member->id,
            'data' => json_encode($data),
            'read_at' => $readAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    /**
     * Get a random sender ID from existing members
     */
    protected function getRandomSenderId(): ?int
    {
        // 20% chance of system notification (no sender)
        if (rand(1, 10) <= 2) {
            return null;
        }

        // Get a random member ID
        $member = Member::where(['type_id' => MemberAccountType::SCHOOL])->inRandomOrder()->first();

        return $member ? $member->id : null;
    }

    /**
     * Get relevant metadata based on notification type
     */
    protected function getMetadataForType(string $type): array
    {
        switch ($type) {
            case 'meeting':
                return [
                    'meeting_id' => rand(1, 100),
                    'location' => 'Room '.rand(101, 199),
                    'date' => Carbon::now()->addDays(rand(1, 14))->format('Y-m-d'),
                    'time' => sprintf('%02d:%02d', rand(8, 17), rand(0, 59)),
                ];

            case 'exam':
                return [
                    'exam_id' => rand(1, 100),
                    'subject' => Arr::random(['Math', 'Science', 'English', 'History', 'Geography']),
                    'date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
                    'duration' => rand(1, 3).' hours',
                ];

            case 'holiday':
                $startDate = Carbon::now()->addDays(rand(1, 30));
                $endDate = (clone $startDate)->addDays(rand(1, 14));

                return [
                    'holiday_name' => Arr::random(['Spring Break', 'Winter Holiday', 'National Day', 'Teacher\'s Day']),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ];

            case 'attendance':
            case 'absence':
            case 'tardiness':
                return [
                    'date' => Carbon::now()->subDays(rand(0, 14))->format('Y-m-d'),
                    'student_id' => rand(1, 100),
                    'status' => Arr::random(['present', 'absent', 'late']),
                ];

            case 'behavior':
            case 'performance':
                return [
                    'report_id' => rand(1, 100),
                    'student_id' => rand(1, 100),
                    'rating' => rand(1, 5),
                    'comments' => 'This is a sample comment for a '.$type.' report.',
                ];

            default:
                return [];
        }
    }
}
