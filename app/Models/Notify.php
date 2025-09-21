<?php

namespace App\Models;

use App\Traits\HijriDateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Notify extends Model
{
    use HasFactory, HasTranslations, HijriDateTrait, SoftDeletes;

    public $translatable = ['title', 'content'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id',
        'notifiable_ids',
        'title',
        'content',
        'creator_id',
        'creator_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'notifiable_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the notify type associated with the notification.
     */
    public function notifyTag()
    {
        return $this->belongsTo(Constant::class, 'tag_id');
    }

    /**
     * Get the creator of the notification.
     */
    public function creator()
    {
        return $this->morphTo();
    }
}
