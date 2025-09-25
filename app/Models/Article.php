<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Article extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['article_type_id', 'title', 'description', 'image', 'published_at', 'active'];

    protected $translatable = ['title', 'description'];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'published_at' => 'date',
        'active' => 'boolean',
    ];

    public function article_type()
    {
        return $this->belongsTo(ArticleType::class);
    }

    public function article_contents()
    {
        return $this->hasMany(ArticleContent::class);
    }
}
