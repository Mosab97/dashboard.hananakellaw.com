<?php

namespace App\Services\API\Filters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait RelationFilterTrait
{
    /**
     * Checks if the key is a relation filter
     * Format: relation__field (e.g., member__name) or relation.subrelation__field (e.g., teachers.member__name)
     *
     * @param  string  $key  The filter key
     * @return bool
     */
    protected function isRelationFilter($key)
    {
        if (strpos($key, '__') === false) {
            return false;
        }

        // Check for nested relation.subrelation__field format
        $parts = explode('__', $key, 2);
        $relationPath = $parts[0];
        $field = $parts[1];

        // Check if this is a direct relation or nested relation
        if (strpos($relationPath, '.') !== false) {
            // This is a nested relation (e.g., teachers.member)
            return array_key_exists($relationPath, $this->relationFields) &&
                in_array($field, $this->relationFields[$relationPath]);
        } else {
            // This is a direct relation (e.g., school)
            return array_key_exists($relationPath, $this->relationFields) &&
                in_array($field, $this->relationFields[$relationPath]);
        }
    }

    /**
     * Filters the query by a relation field
     * Format: relation__field (e.g., school__name) or relation.subrelation__field (e.g., teachers.member__name)
     *
     * @param  Builder  $query
     * @param  string  $key  The relation__field key
     * @param  mixed  $value  The filter value
     */
    protected function filterByRelation($query, $key, $value)
    {
        $parts = explode('__', $key, 2);
        $relationPath = $parts[0];
        $field = $parts[1];

        Log::info("Filtering by relation path: $relationPath, field: $field, value: $value");

        // Check if this is a nested relation
        $relations = explode('.', $relationPath);

        // Define which fields are translatable across all relations
        $translatableFieldsByRelation = $this->getTranslatableRelationFields();

        // Define which fields are date fields across all relations
        $dateFieldsByRelation = $this->getDateRelationFields();

        // Define which fields are boolean fields across all relations
        $booleanFieldsByRelation = $this->getBooleanRelationFields();

        // Define which fields should use partial matching across all relations
        $partialMatchFieldsByRelation = $this->getPartialMatchRelationFields();

        // Check field type and apply appropriate filter
        if (
            isset($translatableFieldsByRelation[$relationPath]) &&
            in_array($field, $translatableFieldsByRelation[$relationPath])
        ) {
            $this->filterByTranslatableRelation($query, $relations, $field, $value);
        } elseif (
            isset($dateFieldsByRelation[$relationPath]) &&
            in_array($field, $dateFieldsByRelation[$relationPath])
        ) {
            $this->filterByRelationDateField($query, $relations, $field, $value);
        } elseif (
            isset($booleanFieldsByRelation[$relationPath]) &&
            in_array($field, $booleanFieldsByRelation[$relationPath])
        ) {
            $this->filterByRelationBooleanField($query, $relations, $field, $value);
        } elseif (
            isset($partialMatchFieldsByRelation[$relationPath]) &&
            in_array($field, $partialMatchFieldsByRelation[$relationPath])
        ) {
            // Partial match for string fields
            $this->filterByRelationPartialMatch($query, $relations, $field, $value);
        } else {
            // Exact match for other fields (IDs, numbers, etc.)
            $this->filterByRelationExactMatch($query, $relations, $field, $value);
        }
    }

    /**
     * Filters the query by a translatable relation field
     *
     * @param  Builder  $query
     * @param  array  $relations  The relation path array
     * @param  string  $field  The field name
     * @param  string  $value  The search value
     */
    protected function filterByTranslatableRelation($query, $relations, $field, $value)
    {
        // Get locales from config
        $locales = config('app.locales');

        if (count($relations) === 1) {
            // Single relation
            $relation = $relations[0];
            $tableName = $this->getTableNameForRelation($relation);

            $query->whereHas($relation, function ($q) use ($field, $value, $locales, $tableName) {
                $q->where(function ($subQuery) use ($field, $value, $locales, $tableName) {
                    foreach ($locales as $locale) {
                        $searchValue = '%'.strtolower($value).'%';
                        $rawQuery = "json_extract(LOWER({$tableName}.{$field}), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
                        $subQuery->orWhereRaw($rawQuery, [$searchValue]);
                    }
                });
            });

            Log::info("Applied translatable relation filter on {$relations[0]}.$field");
        } elseif (count($relations) === 2) {
            // Nested relation (e.g., teachers.member)
            $childTableName = $this->getTableNameForRelation(implode('.', $relations));

            $query->whereHas($relations[0], function ($q) use ($relations, $field, $value, $locales, $childTableName) {
                $q->whereHas($relations[1], function ($subQ) use ($field, $value, $locales, $childTableName) {
                    $subQ->where(function ($subSubQ) use ($field, $value, $locales, $childTableName) {
                        foreach ($locales as $locale) {
                            $searchValue = '%'.strtolower($value).'%';
                            $rawQuery = "json_extract(LOWER({$childTableName}.{$field}), \"$.$locale\") LIKE convert(? using utf8mb4) collate utf8mb4_general_ci";
                            $subSubQ->orWhereRaw($rawQuery, [$searchValue]);
                        }
                    });
                });
            });

            Log::info("Applied translatable nested relation filter on {$relations[0]}.{$relations[1]}.$field");
        }
    }

    /**
     * Filters the query by a relation boolean field
     *
     * @param  Builder  $query
     * @param  array  $relations  The relation path array
     * @param  string  $field  The field name
     * @param  mixed  $value  The boolean value
     */
    protected function filterByRelationBooleanField($query, $relations, $field, $value)
    {
        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if (count($relations) === 1) {
            $tableName = $this->getTableNameForRelation($relations[0]);

            $query->whereHas($relations[0], function ($q) use ($field, $boolValue, $tableName) {
                $q->where("{$tableName}.{$field}", $boolValue);
            });
        } elseif (count($relations) === 2) {
            $childTableName = $this->getTableNameForRelation(implode('.', $relations));

            $query->whereHas($relations[0], function ($q) use ($relations, $field, $boolValue, $childTableName) {
                $q->whereHas($relations[1], function ($subQ) use ($field, $boolValue, $childTableName) {
                    $subQ->where("{$childTableName}.{$field}", $boolValue);
                });
            });
        }

        Log::info("Filtering by relation boolean field $field: ".($boolValue ? 'true' : 'false'));
    }

    /**
     * Filters the query by a relation field with partial matching (LIKE)
     *
     * @param  Builder  $query
     * @param  array  $relations  The relation path array
     * @param  string  $field  The field name
     * @param  string  $value  The search value
     */
    protected function filterByRelationPartialMatch($query, $relations, $field, $value)
    {
        if (count($relations) === 1) {
            $tableName = $this->getTableNameForRelation($relations[0]);

            $query->whereHas($relations[0], function ($q) use ($field, $value, $tableName) {
                $q->where("{$tableName}.{$field}", 'LIKE', "%{$value}%");
            });
        } elseif (count($relations) === 2) {
            $childTableName = $this->getTableNameForRelation(implode('.', $relations));

            $query->whereHas($relations[0], function ($q) use ($relations, $field, $value, $childTableName) {
                $q->whereHas($relations[1], function ($subQ) use ($field, $value, $childTableName) {
                    $subQ->where("{$childTableName}.{$field}", 'LIKE', "%{$value}%");
                });
            });
        }

        Log::info("Filtering by relation field $field with partial match: %{$value}%");
    }

    /**
     * Filters the query by a relation field with exact matching
     *
     * @param  Builder  $query
     * @param  array  $relations  The relation path array
     * @param  string  $field  The field name
     * @param  mixed  $value  The value to match
     */
    protected function filterByRelationExactMatch($query, $relations, $field, $value)
    {
        if (count($relations) === 1) {
            $tableName = $this->getTableNameForRelation($relations[0]);
        } else {
            $tableName = $this->getTableNameForRelation(implode('.', $relations));
        }

        // Check if it's an array of values or a comma-separated string
        if (is_array($value) || strpos($value, ',') !== false) {
            $values = is_array($value) ? $value : explode(',', $value);
            $values = array_map('trim', $values);
            $values = $this->filterArrayForNullValues($values);

            if (count($values) > 0) {
                if (count($relations) === 1) {
                    $query->whereHas($relations[0], function ($q) use ($field, $values, $tableName) {
                        $q->whereIn("{$tableName}.{$field}", $values);
                    });
                } elseif (count($relations) === 2) {
                    $query->whereHas($relations[0], function ($q) use ($relations, $field, $values, $tableName) {
                        $q->whereHas($relations[1], function ($subQ) use ($field, $values, $tableName) {
                            $subQ->whereIn("{$tableName}.{$field}", $values);
                        });
                    });
                }

                Log::info("Filtering by relation field $field with multiple values:", $values);
            }
        } else {
            if (count($relations) === 1) {
                $query->whereHas($relations[0], function ($q) use ($field, $value, $tableName) {
                    $q->where("{$tableName}.{$field}", $value);
                });
            } elseif (count($relations) === 2) {
                $query->whereHas($relations[0], function ($q) use ($relations, $field, $value, $tableName) {
                    $q->whereHas($relations[1], function ($subQ) use ($field, $value, $tableName) {
                        $subQ->where("{$tableName}.{$field}", $value);
                    });
                });
            }

            Log::info("Filtering by relation field $field with single value: $value");
        }
    }

    /**
     * Get the table name for a given relation to avoid ambiguous column references
     *
     * @param  string  $relation  The relation name or path
     * @return string The table name
     */
    protected function getTableNameForRelation($relation)
    {
        return $this->tableMap[$relation] ?? $relation;
    }
}
