<?php

namespace App\Enums;

enum BookType: string
{
    case ZOOM = 'zoom';
    case OFFICE = 'office';

    public function label(): string
    {
        return match ($this) {
            self::ZOOM => t('Zoom'),
            self::OFFICE => t('Office'),
        };
    }


    public static function toArray(): array
    {
        return array_map(function (BookType $bookType) {
            return $bookType->value;
        }, self::cases());
    }
}
