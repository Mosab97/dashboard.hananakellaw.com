<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class SucessStory extends Model
{
    use HasFactory, softDeletes, HasTranslations;

    public $translatable = ['owner_name', 'description'];

    protected $fillable = ['owner_name', 'rate', 'description', 'active'];

    protected $casts = [
        'rate' => 'integer',
        'active' => 'boolean',
    ];

}
