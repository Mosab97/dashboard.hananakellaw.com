<?php

namespace App\Enums\Traits;

use App\Services\Constants\ConstantHandler;

trait ConstantEnumTrait
{
    abstract public function getName(): array;

    protected static function getHandler(): ConstantHandler
    {
        return new ConstantHandler(static::MODULE, static::FIELD);
    }

    public function getFromDatabase()
    {
        return self::getHandler()->getByConstantName($this->value);
    }

    public static function getAllFromDatabase()
    {
        return self::getHandler()->getFromDatabase();
    }

    public static function findInDatabase(string $value)
    {
        return self::getHandler()->getByConstantName($value);
    }

    // public static function getFromDatabase()
    // {
    //     return self::getHandler()->getFromDatabase();
    // }

    public static function getByConstantNameDatabase(string $constantName)
    {
        return self::getHandler()->getByConstantName($constantName);
    }

    public static function clearCache(): void
    {
        self::getHandler()->clearCache();
    }

    public static function refreshCache()
    {
        return self::getHandler()->refreshCache();
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->getName(),

            'active' => $this->isActive(),
        ];

        if (method_exists($this, 'getIcon')) {
            $data['icon'] = $this->getIcon();
        }
        if (method_exists($this, 'getColor')) {
            $data['color'] = $this->getColor();
        }

        return $data;
    }

    public function isActive(): bool
    {
        return true;
    }

    public static function seed(): void
    {
        $items = [];
        foreach (self::cases() as $case) {
            $data = $case->toArray();
            $items[] = [
                'constant_name' => $case->value,
                'name' => $data['name'],
                'value' => $case->value,
                'color' => isset($data['color']) ? $data['color'] : null,
                'active' => $data['active'],
                'icon' => $data['icon'] ?? null,
            ];
        }

        self::getHandler()->seed($items);
    }

    public static function getAllItems(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[$case->value] = $case->toArray();
        }

        return $items;
    }

    /**
     * Creates a constant in the database with the given name, even if it's not an enum case
     *
     * @param  string  $arName  The Arabic name of the grade level
     * @param  string|null  $enName  The English name (will use capitalized Arabic name if null)
     * @return \App\Models\Constant|null The created constant or null if creation failed
     */
    public static function createCustomConstantInDatabase(string $arName, ?string $enName = null): ?\App\Models\Constant
    {
        // Generate a constant_name (slug) from the Arabic name
        $constantName = str_replace(' ', '_', trim(strtolower($arName)));
        // Check if it already exists in the database
        $existingConstant = self::getHandler()->getByConstantName($constantName);
        if ($existingConstant) {
            return $existingConstant;
        }

        // If English name is not provided, use capitalized Arabic name
        $enName = $enName ?? ucfirst(strtolower($arName));

        // Prepare data for the constant
        $attributes = [
            'constant_name' => $constantName,
            'name' => [
                'en' => $enName,
                'ar' => $arName,
            ],
            'value' => $constantName,
            'module' => static::MODULE,
            'field' => static::FIELD,
            'active' => true,
        ];

        // Use the handler to create the constant and clear cache
        $handler = self::getHandler();
        $constant = $handler->updateOrCreate($attributes);

        // Explicitly clear cache
        $handler->clearCache();

        return $constant;
    }
}
