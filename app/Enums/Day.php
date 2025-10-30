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

    public static function toArray(): array
    {
        return array_map(function (Day $day) {
            return $day->value;
        }, self::cases());
    }
}