<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Size extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'restaurant_id',
        'active',
    ];

    protected $casts = [
        'name' => 'json',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    protected $translatable = ['name'];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
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
}
