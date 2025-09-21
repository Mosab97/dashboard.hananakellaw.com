<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasActionButtons;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable
{
    use HasActionButtons, HasApiTokens, HasFactory, HasRoles, HasTranslations, Impersonate, Notifiable, SoftDeletes;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'mobile',
        'branch_id',
        'email',
        'password',
        'active',
        'last_login_at',
    ];

    public const ui = [
        'table' => 'users',
        'route' => 'users',
        's_ucf' => 'User',
        'p_ucf' => 'Users',
        's_lcf' => 'usre',
        'p_lcf' => 'users',
        'view' => 'CP.user-management.users.',
        '_id' => 'user_id',
        'controller_name' => 'UserController',
        'image_path' => 'users',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function canImpersonate()
    {
        // For example
        return $this->id == 1;
    }

    public function branch()
    {
        return $this->belongsTo(Constant::class, 'branch_id');
    }

    public function scopeActive(Builder $query)
    {
        $query->where('active', 1);
    }

    protected function getAvatarAttribute($value)
    {
        return (isset($value) && ! empty($value)) ? asset($value) : asset('media/avatars/blank.png');
    }

    /**
     * Override getActionButtons to customize buttons for Lead model
     */
    protected function getActionButtons(): array
    {
        return [
            $this->getEditButton(),
            $this->getRemoveButton(),
        ];
    }
}
