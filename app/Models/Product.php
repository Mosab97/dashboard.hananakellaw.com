<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
        'price',
        'active',
        'order',
        'restaurant_id',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    protected $translatable = ['name', 'description'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes')->withPivot('price')->withTimestamps()->wherePivot('deleted_at', null);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    // Helper methods
    public function getStatusBadge()
    {
        if ($this->active) {
            return '<span class="badge badge-light-success">Active</span>';
        }

        return '<span class="badge badge-light-danger">Inactive</span>';
    }

    public function getFormattedName()
    {
        if (is_array($this->name)) {
            return $this->name[app()->getLocale()] ?? $this->name['en'] ?? 'N/A';
        }

        return $this->name ?? 'N/A';
    }

    public function getImagePathAttribute()
    {
        $path = $this->image;
        if (!isset($path)) {
            return null;
        }
        if (str_starts_with($path, 'uploads')) {
            return asset($path);
        } else {
            return asset('storage/' . $path);
        }
        // return asset('media/stock/food/img-2.jpg');
    }
}
