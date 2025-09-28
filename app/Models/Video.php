<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Video extends Model
{
    use HasFactory, HasTranslations, softDeletes;

    public $translatable = ['title', 'description'];

    protected $fillable = ['title', 'description', 'path', 'active', 'thumbnail'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getThumbnailPathAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return null;
    }
}
