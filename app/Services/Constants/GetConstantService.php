<?php

namespace App\Services\Constants;

use App\Enums\DropDownFields;
use App\Enums\Modules;

class GetConstantService
{
    public static function get_question_type_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::program_page_module,
            'field' => DropDownFields::question_type,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_program_eligibility_type_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::program_module,
            'field' => DropDownFields::program_eligibility_type,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_program_target_applicants_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::program_module,
            'field' => DropDownFields::program_target_applicants,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_program_category_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::program_module,
            'field' => DropDownFields::program_category,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_main_interst_type_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::main_module,
            'field' => DropDownFields::main_interst_type,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_program_facility_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::program_module,
            'field' => DropDownFields::program_facility,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_program_attachment_type_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::attachment_module,
            'field' => DropDownFields::program_attachment_type,
            'constant_name' => $constant_name,
        ]);
    }

    public static function get_bank_list($constant_name = null)
    {
        return self::getConstantsByFilter([
            'module' => Modules::main_module,
            'field' => DropDownFields::banks,
            'constant_name' => $constant_name,
        ]);
    }

    /**
     * Helper method to get constants by filter
     *
     * @param  array  $filter  The filter array containing module and field
     * @return \Illuminate\Support\Collection
     */
    private static function getConstantsByFilter(array $filter)
    {
        // Remove null values from filter
        $filter = array_filter($filter, function ($value) {
            return ! is_null($value);
        });

        return ConstantService::search($filter);
    }
}
