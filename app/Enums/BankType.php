<?php

namespace App\Enums;

enum BankType: string
{
    use Traits\ConstantEnumTrait;

    public const MODULE = Modules::subscription_module;

    public const FIELD = 'bank_type';

    case ALRAJHI = 'alrajhi';
    case NCB = 'ncb';
    case ALINMA = 'alinma';
    case ALBILAD = 'albilad';
    case ALAHLIUNITED = 'alahli_united';

    public function getName(): array
    {
        return match ($this) {
            self::ALRAJHI => [
                'en' => 'Al Rajhi Bank',
                'ar' => 'مصرف الراجحي',
            ],
            self::NCB => [
                'en' => 'National Commercial Bank',
                'ar' => 'البنك الأهلي التجاري',
            ],
            self::ALINMA => [
                'en' => 'Alinma Bank',
                'ar' => 'مصرف الإنماء',
            ],
            self::ALBILAD => [
                'en' => 'Bank Albilad',
                'ar' => 'بنك البلاد',
            ],
            self::ALAHLIUNITED => [
                'en' => 'Alahli United Bank',
                'ar' => 'البنك الأهلي المتحد',
            ],
        };
    }
}
