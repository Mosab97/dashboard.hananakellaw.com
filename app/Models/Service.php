<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['title', 'description', 'short_description', 'features', 'icon', 'link', 'active'];

    protected $translatable = ['title', 'description', 'short_description'];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'short_description' => 'array',
        'features' => 'array',
        'active' => 'boolean',
    ];

    public function getIconPathAttribute()
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        return null;
    }

    public function scopeSearch($query, $value)
    {
        $query->where(function ($query) use ($value) {
            // Search in translatable name field
            $locales = config('app.locales', []);
            foreach ($locales as $locale) {
                $query->orWhereRaw(
                    "LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) LIKE ?",
                    ['%' . strtolower($value) . '%']
                );
            }
        });
    }
}
