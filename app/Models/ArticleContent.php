<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ArticleContent extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = ['article_id', 'title', 'features', 'active', 'order'];

    protected $translatable = ['title', 'features'];

    protected $casts = [
        'title' => 'array',
        'features' => 'array',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
