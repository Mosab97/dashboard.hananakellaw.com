<?php

namespace App\Services\Constants;

use App\Models\Constant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ConstantService
{
    private const CACHE_PREFIX = 'constants:';

    private const CACHE_TTL = 3600; // 1 hour in seconds

    /**
     * Get a constant by its ID
     */
    public static function getById(int $id): ?Constant
    {
        $cacheKey = self::CACHE_PREFIX."id:{$id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            return Constant::find($id);
        });
    }

    /**
     * Get constants by module
     */
    public static function getByModule(string $module): Collection
    {
        $cacheKey = self::CACHE_PREFIX."module:{$module}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($module) {
            return Constant::where('module', $module)->get();
        });
    }

    /**
     * Get constants by field within a specific module
     */
    public static function getByModuleAndField(string $module, string $field): Collection
    {
        $cacheKey = self::CACHE_PREFIX."module:{$module}:field:{$field}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($module, $field) {
            return Constant::where('module', $module)
                ->where('field', $field)
                ->get();
        });
    }

    /**
     * Get constants by field
     */
    public static function getByField(string $field): Collection
    {
        $cacheKey = self::CACHE_PREFIX."field:{$field}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($field) {
            return Constant::where('field', $field)->get();
        });
    }

    /**
     * Get constant by constant_name
     */
    public static function getByConstantName(string $constantName): ?Constant
    {
        $cacheKey = self::CACHE_PREFIX."name:{$constantName}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($constantName) {
            return Constant::where('constant_name', $constantName)->first();
        });
    }

    /**
     * Search constants with multiple criteria
     */
    public static function search(array $criteria): Collection
    {
        $cacheKey = self::CACHE_PREFIX.'search:'.md5(json_encode($criteria));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($criteria, $cacheKey) {
            $query = Constant::query();

            Log::info('Starting to build search query with criteria:', $criteria);

            if (isset($criteria['id'])) {
                $query->where('id', $criteria['id']);
                logQuery($query, 'After adding ID filter');
            }

            if (isset($criteria['module'])) {
                $query->where('module', $criteria['module']);
                logQuery($query, 'After adding module filter');
            }

            if (isset($criteria['field'])) {
                $query->where('field', $criteria['field']);
                logQuery($query, 'After adding field filter');
            }

            if (isset($criteria['constant_name'])) {
                // $query->where('constant_name', 'LIKE', "%{$criteria['constant_name']}%");
                $query->where('constant_name', "{$criteria['constant_name']}");
                logQuery($query, 'After adding constant_name filter');
            }

            if (isset($criteria['name'])) {
                $query->where(function ($q) use ($criteria) {
                    $q->where('name_en', 'LIKE', "%{$criteria['name']}%")
                        ->orWhere('name_ar', 'LIKE', "%{$criteria['name']}%");
                });
                logQuery($query, 'After adding name filter');
            }

            if (isset($criteria['value'])) {
                $query->where('value', $criteria['value']);
                logQuery($query, 'After adding value filter');
            }

            if (isset($criteria['parent_id'])) {
                $query->where('parent_id', $criteria['parent_id']);
                logQuery($query, 'After adding parent_id filter');
            }

            // Log final query before execution
            $finalQuery = logQuery($query, 'Final search query before execution');
            Log::info('Executing final query', [
                'query' => $finalQuery,
                'criteria' => $criteria,
            ]);

            $results = $query->get();

            Log::info('Query execution completed', [
                'results_count' => $results->count(),
                'cache_key' => $cacheKey,
            ]);

            return $results;
        });
    }

    /**
     * Get localized name based on locale
     */
    public static function getLocalizedName(int $id, ?string $locale = null): string
    {
        $constant = self::getById($id);

        if (! $constant) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();
        $fieldName = 'name_'.$locale;

        return $constant->{$fieldName} ?? '';
    }

    /**
     * Get value by constant ID
     */
    public static function getValue(int $id): string
    {
        $constant = self::getById($id);

        return $constant ? $constant->value : '';
    }

    /**
     * Clear all related caches for a constant
     */
    public static function clearCache(Constant $constant): void
    {
        Log::info('Starting cache clear for constant:', [
            'id' => $constant->id,
            'module' => $constant->module,
            'field' => $constant->field,
            'constant_name' => $constant->constant_name,
        ]);

        // Clear existing caches
        $idCacheKey = self::CACHE_PREFIX."id:{$constant->id}";
        Cache::forget($idCacheKey);
        Log::info('Cleared cache by ID:', ['cache_key' => $idCacheKey]);

        $moduleCacheKey = self::CACHE_PREFIX."module:{$constant->module}";
        Cache::forget($moduleCacheKey);
        Log::info('Cleared cache by module:', ['cache_key' => $moduleCacheKey]);

        $fieldCacheKey = self::CACHE_PREFIX."field:{$constant->field}";
        Cache::forget($fieldCacheKey);
        Log::info('Cleared cache by field:', ['cache_key' => $fieldCacheKey]);

        $moduleFieldCacheKey = self::CACHE_PREFIX."module:{$constant->module}:field:{$constant->field}";
        Cache::forget($moduleFieldCacheKey);
        Log::info('Cleared cache by module and field:', ['cache_key' => $moduleFieldCacheKey]);

        // Clear search cache for this module and field combination
        $searchCriteria = [
            'module' => $constant->module,
            'field' => $constant->field,
        ];
        $searchCacheKey = self::CACHE_PREFIX.'search:'.md5(json_encode($searchCriteria));
        Cache::forget($searchCacheKey);
        Log::info('Cleared search cache:', ['cache_key' => $searchCacheKey]);

        if ($constant->constant_name) {
            $nameCacheKey = self::CACHE_PREFIX."name:{$constant->constant_name}";
            Cache::forget($nameCacheKey);
            Log::info('Cleared cache by constant name:', ['cache_key' => $nameCacheKey]);
        }

        Log::info('Completed clearing all caches for constant ID: '.$constant->id);
    }

    /**
     * Get constant name based on module, field and constant_name
     *
     * @param  array  $criteria  Should contain module, field and constant_name
     * @param  string|null  $locale  Locale to get name for
     */
    public static function getName(array $criteria, ?string $locale = null): string
    {
        if (! isset($criteria['module']) || ! isset($criteria['field']) || ! isset($criteria['constant_name'])) {
            Log::warning('Missing required criteria for getName', $criteria);

            return '';
        }

        $constant = self::search($criteria)->first();

        if (! $constant) {
            Log::warning('No constant found for criteria', $criteria);

            return '';
        }

        $locale = $locale ?? app()->getLocale();

        return $constant->getTranslation('name', $locale, false) ?? '';
    }
}
