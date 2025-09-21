<?php

namespace App\Services\API\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait PolymorphicFilterTrait
{
    /**
     * Check if a key is a top-level polymorphic type field
     *
     * @param  string  $key  The filter key
     * @return bool
     */
    protected function isPolymorphicTypeField($key)
    {
        foreach ($this->polymorphicRelations as $relation => $config) {
            if ($key === $config['type_field']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the relation name for a polymorphic type field
     *
     * @param  string  $key  The filter key
     * @return string|null
     */
    protected function getRelationForTypeField($key)
    {
        foreach ($this->polymorphicRelations as $relation => $config) {
            if ($key === $config['type_field']) {
                return $relation;
            }
        }

        return null;
    }

    /**
     * Filters the query by a polymorphic relation field
     * Format: relation_type__field (e.g., attendable_student__name)
     *
     * @param  Builder  $query
     * @param  string  $key  The filter key
     * @param  mixed  $value  The filter value
     */
    protected function filterByPolymorphicRelation($query, $key, $value)
    {
        foreach ($this->polymorphicRelations as $relation => $config) {
            $pattern = '/^'.$relation.'_([^_]+)__(.+)$/';
            if (preg_match($pattern, $key, $matches)) {
                $type = $matches[1];
                $field = $matches[2];

                // Check if the type is defined in the polymorphic relation config
                if (isset($config['types'][$type])) {
                    $className = $config['types'][$type];
                    $typeField = $config['type_field'];
                    $relationTableKey = $relation.'.'.$type;
                    $tableName = $this->tableMap[$relationTableKey] ?? null;

                    Log::info("Filtering by polymorphic relation: $relation, type: $type, field: $field, value: $value, table: $tableName");

                    // Apply the filter based on the polymorphic type and field
                    $query->where($this->tableName.'.'.$typeField, $className)
                        ->whereHas($relation, function ($q) use ($field, $value, $tableName) {
                            // Use the table name to avoid ambiguous column references
                            if ($tableName && in_array($field, $this->getTranslatableFields())) {
                                // For translatable fields
                                $locales = config('app.locales');
                                $q->where(function ($subQuery) use ($field, $value, $locales, $tableName) {
                                    foreach ($locales as $locale) {
                                        $searchValue = '%'.strtolower($value).'%';
                                        $rawQuery = "json_extract(LOWER({$tableName}.{$field}), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
                                        $subQuery->orWhereRaw($rawQuery, [$searchValue]);
                                    }
                                });
                            } elseif ($tableName) {
                                // For regular fields, explicitly include the table name
                                $fieldWithTable = $tableName.'.'.$field;
                                if (in_array($field, $this->getPartialMatchFields())) {
                                    $q->where($fieldWithTable, 'LIKE', "%{$value}%");
                                } else {
                                    $q->where($fieldWithTable, $value);
                                }
                            } else {
                                // Fallback if no table is specified
                                if (in_array($field, $this->getPartialMatchFields())) {
                                    $q->where($field, 'LIKE', "%{$value}%");
                                } else {
                                    $q->where($field, $value);
                                }
                            }
                        });

                    return; // Once we've handled the filter, we can return
                }
            }
        }

        Log::warning("Unrecognized polymorphic relation filter: $key");
    }

    /**
     * Checks if the filter key is for a polymorphic relation
     * Format: relation_type__field (e.g., attendable_student__name)
     *
     * @param  string  $key  The filter key
     * @return bool
     */
    protected function isPolymorphicRelationFilter($key)
    {
        foreach ($this->polymorphicRelations as $relation => $config) {
            $pattern = '/^'.$relation.'_([^_]+)__(.+)$/';
            if (preg_match($pattern, $key, $matches)) {
                $type = $matches[1];
                // Check if the type is defined in the polymorphic relation config
                if (isset($config['types'][$type])) {
                    return true;
                }
            }
        }

        return false;
    }
}
