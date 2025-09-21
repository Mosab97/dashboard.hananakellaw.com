<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\DateTimeFilterTrait;
use App\Services\API\Filters\Traits\FieldFilterTrait;
use App\Services\API\Filters\Traits\PolymorphicFilterTrait;
use App\Services\API\Filters\Traits\RelationFilterTrait;
use App\Services\API\Filters\Traits\TimeFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

abstract class BaseFilterService
{
    use DateTimeFilterTrait;
    use FieldFilterTrait;
    use HijriDateTrait;
    use PolymorphicFilterTrait;
    use RelationFilterTrait;
    use TimeFilterTrait;

    /**
     * List of translatable fields in the model
     */
    protected $translatableFields = [];

    /**
     * List of relation fields for filtering
     */
    protected $relationFields = [];

    /**
     * List of foreign key fields in the model
     */
    protected $foreignKeyFields = [];

    /**
     * List of date fields in the model
     */
    protected $dateFields = [];

    /**
     * List of time fields in the model
     */
    protected $timeFields = [];

    /**
     * List of boolean fields in the model
     */
    protected $booleanFields = [];

    /**
     * List of JSON array fields in the model
     */
    protected $jsonArrayFields = [];

    /**
     * List of regular fields in the model (for exact matching)
     */
    protected $regularFields = [];

    /**
     * List of fields that should use partial matching (LIKE)
     */
    protected $partialMatchFields = [];

    /**
     * The main table name for the model being filtered
     */
    protected $tableName = '';

    /**
     * Map relation names to their table names
     */
    protected $tableMap = [];

    /**
     * Polymorphic relation mappings
     */
    protected $polymorphicRelations = [];

    /**
     * Applies all available filters to the query based on params.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $params
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply filters with params:', $params);

        // Process polymorphic relation filters like attendable_student__name first
        foreach ($params as $key => $value) {
            if ($this->isPolymorphicRelationFilter($key)) {
                $this->filterByPolymorphicRelation($query, $key, $value);
                unset($params[$key]); // Remove processed key to avoid double processing
            }
        }

        // Process all regular filters
        foreach ($params as $key => $value) {
            if ($value !== null && $value !== '') {
                Log::info("Applying filter for: $key with value:", ['value' => $value]);

                // Determine filter type based on the field
                if ($this->isPolymorphicTypeField($key)) {
                    // Handle polymorphic type field
                    $relation = $this->getRelationForTypeField($key);
                    $config = $this->polymorphicRelations[$relation];
                    $typeMap = $config['types'];
                    $type = strtolower($value);

                    if (isset($typeMap[$type])) {
                        $modelClass = $typeMap[$type];
                        $query->where($this->tableName.'.'.$key, $modelClass);
                        Log::info("Filtering by polymorphic type field $key using mapped class: $modelClass");
                    } else {
                        $query->where($this->tableName.'.'.$key, $value);
                        Log::info("Filtering by polymorphic type field $key using direct value: $value");
                    }
                } elseif (in_array($key, $this->translatableFields)) {
                    $this->filterByTranslatable($query, $key, $value);
                } elseif (in_array($key, $this->foreignKeyFields)) {
                    $this->filterByForeignKey($query, $key, $value);
                } elseif (in_array($key, $this->dateFields)) {
                    $this->filterByDate($query, $key, $value);
                } elseif (in_array($key, $this->timeFields)) {
                    $this->filterByTime($query, $key, $value);
                } elseif (in_array($key, $this->booleanFields)) {
                    $this->filterByBoolean($query, $key, $value);
                } elseif (in_array($key, $this->jsonArrayFields)) {
                    $this->filterByJsonArray($query, $key, $value);
                } elseif (in_array($key, $this->regularFields)) {
                    $this->filterByRegularField($query, $key, $value);
                } elseif ($this->isRelationFilter($key)) {
                    $this->filterByRelation($query, $key, $value);
                } elseif (method_exists($this, 'filterBy'.ucfirst($key))) {
                    // Custom filter method for specific fields
                    $method = 'filterBy'.ucfirst($key);
                    $this->$method($query, $value);
                } else {
                    Log::warning("Unknown filter key: $key");
                }
            } else {
                Log::info("Skipping filter for: $key as the value is null or empty.");
            }
        }

        // Log the final SQL query and its bindings.
        if (function_exists('logQuery')) {
            logQuery($query);
        }

        Log::info('All filters applied.');

        return $query;
    }

    /**
     * Get all translatable fields across models
     *
     * @return array
     */
    protected function getTranslatableFields()
    {
        // This should be overridden in child classes if needed
        return ['name'];
    }

    /**
     * Get all partial match fields across models
     *
     * @return array
     */
    protected function getPartialMatchFields()
    {
        // This should be overridden in child classes if needed
        return ['id_number', 'email', 'phone_number'];
    }

    /**
     * Get all date fields across models
     *
     * @return array
     */
    protected function getDateFields()
    {
        // This should be overridden in child classes if needed
        return ['created_at', 'updated_at', 'date_of_birth', 'graduation_date'];
    }

    /**
     * Get all boolean fields across models
     *
     * @return array
     */
    protected function getBooleanFields()
    {
        // This should be overridden in child classes if needed
        return ['is_active', 'active', 'is_verified'];
    }

    /**
     * Get mappings of relation paths to their translatable fields
     *
     * @return array
     */
    protected function getTranslatableRelationFields()
    {
        return [];
    }

    /**
     * Get mappings of relation paths to their date fields
     *
     * @return array
     */
    protected function getDateRelationFields()
    {
        return [];
    }

    /**
     * Get mappings of relation paths to their boolean fields
     *
     * @return array
     */
    protected function getBooleanRelationFields()
    {
        return [];
    }

    /**
     * Get mappings of relation paths to their fields that should use partial matching
     *
     * @return array
     */
    protected function getPartialMatchRelationFields()
    {
        return [];
    }

    /**
     * Filter array for null values and empty strings.
     *
     * @param  array  $values
     * @return array
     */
    protected function filterArrayForNullValues($values)
    {
        return array_filter($values, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
