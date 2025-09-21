<?php

namespace App\Services\Constants;

use App\Models\Constant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConstantHandler
{
    protected string $module;

    protected string $field;

    protected ?string $cachePrefix;

    protected int $cacheTTL;

    public function __construct(string $module, string $field, ?string $cachePrefix = null, int $cacheTTL = 86400)
    {
        $this->module = $module;
        $this->field = $field;
        $this->cachePrefix = $cachePrefix;
        $this->cacheTTL = $cacheTTL;
    }

    public function getCacheKey(): string
    {
        return sprintf(
            'constants:%s_%s',
            strtolower($this->module),
            strtolower($this->field)
        );
    }

    public function getConstantNameCacheKey(string $constantName): string
    {
        return sprintf(
            'constants:%s_%s_%s',
            strtolower($this->module),
            strtolower($this->field),
            strtolower($constantName)
        );
    }

    protected function clearAllRelatedCaches(): void
    {
        // Clear the main cache key
        $mainCacheKey = $this->getCacheKey();
        Cache::forget($mainCacheKey);
        // Log::debug('Cleared main cache key', [
        //     'cache_key' => $mainCacheKey
        // ]);

        // Common column combinations that might be cached
        $commonColumnSets = [
            ['id', 'constant_name'],
            ['id', 'name'],
            ['id', 'constant_name', 'name'],
            ['id', 'constant_name', 'name', 'value'],
            // Add other common column combinations as needed
        ];

        // Clear cache for common column combinations
        foreach ($commonColumnSets as $columns) {
            sort($columns);
            $columnCacheKey = $this->getCacheKey().':'.implode('_', $columns);
            Cache::forget($columnCacheKey);
            // Log::debug('Cleared column combination cache', [
            //     'cache_key' => $columnCacheKey,
            //     'columns' => $columns
            // ]);
        }

        // Clear constant-specific caches
        $constants = Constant::where('module', $this->module)
            ->where('field', $this->field)
            ->get(['constant_name']);

        // Log::debug('Found constants to clear cache for', [
        //     'count' => $constants->count(),
        //     'module' => $this->module,
        //     'field' => $this->field
        // ]);

        foreach ($constants as $constant) {
            $constantCacheKey = $this->getConstantNameCacheKey($constant->constant_name);
            Cache::forget($constantCacheKey);
            // Log::debug('Cleared constant-specific cache', [
            //     'cache_key' => $constantCacheKey,
            //     'constant_name' => $constant->constant_name
            // ]);
        }

        // Log::debug('Completed clearing all related caches', [
        //     'module' => $this->module,
        //     'field' => $this->field,
        //     'total_keys_cleared' => 1 + count($commonColumnSets) + $constants->count()
        // ]);
    }

    public function getFromDatabase(?array $columns = null): Collection
    {
        // Create a unique cache key that includes the columns
        $cacheKey = $this->getCacheKey();

        if ($columns) {
            // Sort columns to ensure consistent cache keys regardless of array order
            sort($columns);
            // $cacheKey .= ':' . implode('_', $columns);
        }

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($columns) {
            $query = Constant::where('module', $this->module)
                ->where('field', $this->field);

            if ($columns) {
                $query->select($columns);
            }

            $query->orderByRaw('COALESCE(CAST(`order` as SIGNED), 99999)')
                ->orderBy('created_at');

            // Log::debug('Constant Query', [
            //     'module' => $this->module,
            //     'field' => $this->field,
            //     'columns' => $columns,
            //     'cache_key' => $cacheKey,
            //     'sql' => $query->toSql(),
            //     'bindings' => $query->getBindings()
            // ]);

            return $query->get();
        });
    }

    public function updateOrCreate(array $data): Constant
    {
        $attributes = $this->prepareAttributes($data);

        $criteria = [
            'id' => $data['constant_id'] ?? null,
            'module' => $this->module,
            'field' => $this->field,
            'constant_name' => $data['constant_name'] ?? null,
        ];

        // Remove null values from criteria
        $criteria = array_filter($criteria, fn ($value) => ! is_null($value));

        $constant = Constant::updateOrCreate($criteria, $attributes);

        // Clear all related caches
        $this->clearAllRelatedCaches();

        // Log::debug('Updated/Created constant and cleared caches', [
        //     'constant_id' => $constant->id,
        //     'module' => $this->module,
        //     'field' => $this->field
        // ]);

        return $constant;
    }

    public function delete(int|array $ids): bool
    {
        $result = Constant::whereIn('id', (array) $ids)
            ->where('module', $this->module)
            ->where('field', $this->field)
            ->delete();

        // Clear all related caches
        $this->clearAllRelatedCaches();

        // Log::debug('Deleted constants and cleared caches', [
        //     'ids' => (array)$ids,
        //     'module' => $this->module,
        //     'field' => $this->field,
        //     'result' => $result
        // ]);

        return $result > 0;
    }

    public function seed(array $items): void
    {
        Log::info("Starting {$this->field} Seeding...");

        try {
            DB::beginTransaction();

            foreach ($items as $item) {
                $attributes = $this->prepareAttributes($item);

                Constant::updateOrCreate(
                    [
                        'constant_name' => $attributes['constant_name'],
                        'module' => $this->module,
                        'field' => $this->field,
                    ],
                    $attributes
                );
            }

            DB::commit();

            // Clear all related caches after successful seeding
            $this->clearAllRelatedCaches();

            // Log::info("{$this->field} Seeding Completed Successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error during seeding {$this->field}: ".$e->getMessage());
            throw $e;
        }
    }

    public function getByConstantName(string $constantName): ?Constant
    {
        $cacheKey = $this->getConstantNameCacheKey($constantName);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($constantName) {
            return Constant::where('module', $this->module)
                ->where('field', $this->field)
                ->where('constant_name', $constantName)
                ->first();
        });
    }

    public function clearCache(): void
    {
        $this->clearAllRelatedCaches();
    }

    public function refreshCache(): Collection
    {
        $this->clearCache();

        return $this->getFromDatabase();
    }

    protected function prepareAttributes(array $data): array
    {
        $name = [
            'en' => $data['name_en'] ?? ($data['name']['en'] ?? ''),
            'ar' => $data['name_ar'] ?? ($data['name']['ar'] ?? ''),
        ];

        $description = [
            'en' => $data['description_en'] ?? ($data['description']['en'] ?? ''),
            'ar' => $data['description_ar'] ?? ($data['description']['ar'] ?? ''),
        ];

        return [
            'constant_name' => $data['constant_name'] ?? str_replace(' ', '_', trim(strtolower($name['en']))),
            'module' => $this->module,
            'field' => $this->field,
            'name' => $name,
            'description' => $description,
            'value' => $data['value'] ?? $data['constant_value'] ?? null,
            'color' => $data['color'] ?? null,
            'order' => $data['order'] ?? null,
            'icon' => $data['icon'] ?? null,
            'parent_id' => $data['parent_id'] ?? null,
            'active' => $data['active'] ?? true,
        ];
    }
}
