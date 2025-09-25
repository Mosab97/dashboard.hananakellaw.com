<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Models\Article;

class ArticleType extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['name', 'active'];

    protected $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
        'active' => 'boolean',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
