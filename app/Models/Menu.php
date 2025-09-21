<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'route',
        'icon_svg',
        'parent_id',
        'order',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function routeList(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => explode('|', $attributes['route']),
        );
    }

    protected function permissionName(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    if (is_null($value)) {
                        return null;
                    }

                    return explode('|', $value);
                } catch (\Throwable $th) {
                    return null; // or [] if you prefer empty array as fallback
                }
            }
        );
    }
}
