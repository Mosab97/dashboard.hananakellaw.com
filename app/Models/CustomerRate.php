<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CustomerRate extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['name', 'description', 'rate', 'active', 'order'];

    protected $translatable = ['name', 'description'];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'rate' => 'integer',
        'active' => 'boolean',
        'order' => 'integer',
    ];
}
