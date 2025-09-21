<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Slider extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['title', 'description', 'image', 'link', 'active'];

    protected $translatable = ['title', 'description'];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

    public function getImagePathAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
