<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'icon',
        'active',
        'order',
        'restaurant_id',
    ];

    protected $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the restaurant that owns the category
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to get categories for a specific restaurant
     */
    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Scope to order categories by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }




    /**
     * Get the status badge with color
     */
    public function getStatusBadge()
    {
        $color = $this->active ? 'success' : 'danger';
        $text = $this->active ? t('Active') : t('Inactive');

        return '<span class="badge badge-light-' . $color . '">' . $text . '</span>';
    }

    public function getImagePathAttribute()
    {
        if ($this->image) {
            return asset($this->image);
        }
        return null;
    }
    /**
     * Get icon URL
     */
    public function getIconUrl()
    {
        if ($this->icon) {
            return asset($this->icon);
        }

        return null;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Auto-set order if not provided
            if (is_null($category->order)) {
                $maxOrder = static::where('restaurant_id', $category->restaurant_id)->max('order');
                $category->order = $maxOrder ? $maxOrder + 1 : 1;
            }
        });
    }
}
