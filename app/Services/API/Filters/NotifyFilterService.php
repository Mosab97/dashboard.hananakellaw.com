<?php

namespace App\Services\API\Filters;

use App\Services\API\Filters\Traits\HelperFilterTrait;
use App\Traits\HijriDateTrait;
use Illuminate\Support\Facades\Log;

class NotifyFilterService
{
    use HelperFilterTrait, HijriDateTrait;

    /**
     * Applies all available filters to the query based on params.
     */
    public function applyFilters($query, $params)
    {
        Log::info('Starting to apply notify filters with params:', is_array($params) ? $params : ['params' => $params]);

        // Special handling for search_key and search_value pair
        if (isset($params['search_key']) || isset($params['search_value'])) {
            $searchKey = $params['search_key'] ?? null;
            $searchValue = $params['search_value'] ?? null;

            if ($searchKey !== null && $searchValue !== null) {
                // Both parameters are present
                Log::info('Applying dynamic search', [
                    'key' => $searchKey,
                    'value' => $searchValue,
                    'query_type' => 'dynamic_search',
                ]);

                $this->applyDynamicFilter($query, $searchKey, $searchValue);
                Log::info('Dynamic search filter applied successfully');
            } else {
                // One parameter is missing - apply default search
                $valueToSearch = $searchValue ?? $searchKey;

                if ($valueToSearch !== null) {
                    Log::info('Applying default search with partial parameters', [
                        'search_key_present' => isset($params['search_key']),
                        'search_value_present' => isset($params['search_value']),
                        'value_used' => $valueToSearch,
                        'query_type' => 'default_search',
                    ]);

                    // Apply default search (search in title, content, and ID)
                    $query->where(function ($q) use ($valueToSearch) {
                        $this->searchInTranslatableFields($q, 'notifies.title', $valueToSearch);
                        $q->orWhere(function ($subQuery) use ($valueToSearch) {
                            $this->searchInTranslatableFields($subQuery, 'notifies.content', $valueToSearch);
                        });
                        $q->orWhere('notifies.id', 'LIKE', "%{$valueToSearch}%");
                    });

                    Log::info('Default search applied successfully');
                }
            }
        } else {
            Log::info('No search parameters found');
        }

        Log::info('All notify filters applied successfully');

        return $query;
    }

    /**
     * Applies a dynamic filter based on search_key and search_value
     */
    protected function applyDynamicFilter($query, $key, $value)
    {
        Log::debug('Processing dynamic filter', [
            'key' => $key,
            'value' => $value,
            'method' => 'applyDynamicFilter',
        ]);

        // Whitelist of allowed fields and relations for security
        $allowedFields = [
            'id',
            'title',
            'content',
            'tag_id',
            'creator_id',
            'creator_type',
            'created_at',
            'notifiable_ids',
        ];

        $allowedRelations = [
            'notifyTag',
            'creator',
        ];

        // Check if this is a simple field or relation search
        if (in_array($key, $allowedFields)) {
            Log::debug('Direct field search detected', ['field' => $key]);

            // Direct field search
            switch ($key) {
                case 'title':
                    Log::debug('Applying translatable title filter');
                    $this->searchInTranslatableFields($query, 'notifies.title', $value);
                    Log::debug('Translatable title filter applied');
                    break;

                case 'content':
                    Log::debug('Applying translatable content filter');
                    $this->searchInTranslatableFields($query, 'notifies.content', $value);
                    Log::debug('Translatable content filter applied');
                    break;

                case 'id':
                case 'tag_id':
                case 'creator_id':
                    // For IDs, check if it's a comma-separated list
                    if (strpos($value, ',') !== false) {
                        Log::debug('Multiple ID values detected');
                        $this->filterByForeignKey($query, $key, $value);
                    } else {
                        Log::debug('Single ID value detected');
                        $query->where("notifies.$key", $value);
                    }
                    Log::debug('ID filter applied');
                    break;

                case 'notifiable_ids':
                    Log::debug('Searching in JSON array field notifiable_ids');
                    // For searching in JSON array field
                    $query->whereJsonContains('notifies.notifiable_ids', $value);
                    Log::debug('JSON array search applied');
                    break;

                case 'created_at':
                    Log::debug('Applying date filter for created_at');
                    $this->filterByDateField($query, $key, $value);
                    Log::debug('Date filter applied');
                    break;

                case 'creator_type':
                    Log::debug('Applying creator_type filter');
                    $query->where('notifies.creator_type', $value);
                    Log::debug('Creator type filter applied');
                    break;

                default:
                    Log::debug('Applying default field search');
                    $this->searchInTranslatableFields($query, 'notifies.title', $value);
                    $query->orWhere(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'notifies.content', $value);
                    });
                    $query->orWhere('notifies.id', 'LIKE', "%{$value}%");
                    Log::debug('Default field search applied');
                    break;
            }
        } elseif (in_array($key, $allowedRelations)) {
            Log::debug('Relation search detected', ['relation' => $key]);

            // Relation search - handle each relation specially
            $this->filterByRelationName($query, $key, $value);
            Log::debug('Relation search filter applied');
        } else {
            Log::warning('Unauthorized field or relation attempted', [
                'key' => $key,
                'allowed_fields' => $allowedFields,
                'allowed_relations' => $allowedRelations,
            ]);

            return;
        }

        Log::debug('Dynamic filter processing completed', [
            'key' => $key,
        ]);
    }

    /**
     * Filter by the name of a related entity
     */
    private function filterByRelationName($query, $relation, $value)
    {
        Log::debug('Filtering by relation name', [
            'relation' => $relation,
            'value' => $value,
            'method' => 'filterByRelationName',
        ]);

        switch ($relation) {
            case 'notifyTag':
                Log::debug('Searching notify tag by name');
                $query->whereHas('notifyTag', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $this->searchInTranslatableFields($subQuery, 'constants.name', $value);
                    });
                });
                Log::debug('Notify tag search applied');
                break;

            case 'creator':
                Log::debug('Searching creator');
                // This is more complex since it's a morphTo relation
                // We need to determine which creator types to search in
                $query->where(function ($q) use ($value) {
                    // Add different creator types as needed
                    $q->whereHasMorph(
                        'creator',
                        ['App\\Models\\Member', 'App\\Models\\TeacherProfile', 'App\\Models\\Student'],
                        function ($typeQuery, $type) use ($value) {
                            if ($type === 'App\\Models\\Member') {
                                $this->searchInTranslatableFields($typeQuery, 'members.name', $value);
                            } elseif ($type === 'App\\Models\\TeacherProfile') {
                                $typeQuery->whereHas('member', function ($memberQuery) use ($value) {
                                    $this->searchInTranslatableFields($memberQuery, 'members.name', $value);
                                });
                            } elseif ($type === 'App\\Models\\Student') {
                                $this->searchInTranslatableFields($typeQuery, 'students.name', $value);
                            }
                        }
                    );
                });
                Log::debug('Creator search applied');
                break;
        }

        Log::info('Relation filter applied successfully', [
            'relation' => $relation,
        ]);
    }
}
