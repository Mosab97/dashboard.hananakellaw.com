<?php

namespace App\Enums;

enum Day: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    public function label(): string
    {
        return match ($this) {
            self::SATURDAY => t('Saturday'),
            self::SUNDAY => t('Sunday'),
            self::MONDAY => t('Monday'),
            self::TUESDAY => t('Tuesday'),
            self::WEDNESDAY => t('Wednesday'),
            self::THURSDAY => t('Thursday'),
            self::FRIDAY => t('Friday'),
        };
    }
    public function dayOfWeek(): int
    {
        return match ($this) {
            self::SUNDAY => 0,
            self::MONDAY => 1,
            self::TUESDAY => 2,
            self::WEDNESDAY => 3,
            self::THURSDAY => 4,
            self::FRIDAY => 5,
            self::SATURDAY => 6,
        };
    }

    public static function toArray(): array
    {
        return array_map(function (Day $day) {
            return $day->value;
        }, self::cases());
    }
}