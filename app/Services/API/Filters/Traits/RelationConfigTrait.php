<?php

namespace App\Services\API\Filters\Traits;

trait RelationConfigTrait
{
    /**
     * Get mappings of relation paths to their translatable fields
     *
     * @return array
     */
    protected function getTranslatableRelationFields()
    {
        return [
            'classroom' => ['name'],
            'status' => ['name'],
            'school' => ['name'],
            'creator' => ['name'],
            'attendable' => ['name'],
        ];
    }

    /**
     * Get mappings of relation paths to their date fields
     *
     * @return array
     */
    protected function getDateRelationFields()
    {
        return [
            'attendable.student' => ['date_of_birth'],
            'attendable.teacher' => ['graduation_date'],
        ];
    }

    /**
     * Get mappings of relation paths to their boolean fields
     *
     * @return array
     */
    protected function getBooleanRelationFields()
    {
        return [
            'creator' => ['is_verified', 'active'],
        ];
    }

    /**
     * Get mappings of relation paths to their fields that should use partial matching
     *
     * @return array
     */
    protected function getPartialMatchRelationFields()
    {
        return [
            'creator' => ['email', 'phone_number'],
            'attendable' => ['id_number', 'email', 'phone_number'],
        ];
    }
}
