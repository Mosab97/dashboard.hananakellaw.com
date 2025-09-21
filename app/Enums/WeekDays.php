<?php

namespace App\Enums;

enum WeekDays: string
{
    use Traits\ConstantEnumTrait;

    public const MODULE = Modules::main_module;

    public const FIELD = 'week_days';

    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    public function getName(): array
    {
        return match ($this) {
            self::SATURDAY => [
                'en' => 'Saturday',
                'ar' => 'السبت',
            ],
            self::SUNDAY => [
                'en' => 'Sunday',
                'ar' => 'الأحد',
            ],
            self::MONDAY => [
                'en' => 'Monday',
                'ar' => 'الاثنين',
            ],
            self::TUESDAY => [
                'en' => 'Tuesday',
                'ar' => 'الثلاثاء',
            ],
            self::WEDNESDAY => [
                'en' => 'Wednesday',
                'ar' => 'الأربعاء',
            ],
            self::THURSDAY => [
                'en' => 'Thursday',
                'ar' => 'الخميس',
            ],
            self::FRIDAY => [
                'en' => 'Friday',
                'ar' => 'الجمعة',
            ],
        };
    }
}
