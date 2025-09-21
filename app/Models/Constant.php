<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\Translatable\HasTranslations;

class Constant extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'value',
        'module',
        'field',
        'color',
        'constant_name',
        'description',
        'parent_id',
        'icon',
        'active',
        'order',

    ];

    public $translatable = ['name', 'description'];

    public const ui = [
        'table' => 'constants',
        'route' => 'constants',
        's_ucf' => 'Constant',
        'p_ucf' => 'Constants',
        's_lcf' => 'constant',
        'p_lcf' => 'constants',
        'view' => 'CP.constants.',
        '_id' => 'constant_id',
        'controller_name' => 'ConstantController',
        'image_path' => 'constants',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    /**
     * Get the parent constant
     */
    public function parent()
    {
        return $this->belongsTo(Constant::class, 'parent_id');
    }

    /**
     * Get the children constants
     */
    public function children()
    {
        return $this->hasMany(Constant::class, 'parent_id');
    }

    public function getActionButtonsAttribute()
    {
        // First log for debugging
        Log::info('getActionButtonsAttribute', [
            'id' => $this->id,
            'constant' => $this->field,
            'module' => $this->module,
        ]);

        // Guard against empty field or module
        if (empty($this->field) || empty($this->module)) {
            Log::info('getActionBuss s sttonsAttribute', [
                'id' => $this,

            ]);

            return '';
        }

        // Create the edit button only if we have valid parameters
        $editBtn = '<a title="'.t('Edit '.self::ui['s_ucf']).'"
            href="'.route('settings.constants.edit', [
            'constant' => $this->field,
            'module' => $this->module,
        ]).'"
            class="btn btn-icon btn-active-light-primary w-30px h-30px btnUpdateConstant">
            <span class="svg-icon svg-icon-3">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z" fill="currentColor"/>
                    <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z" fill="currentColor"/>
                    <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z" fill="currentColor"/>
                </svg>
            </span>
        </a>';

        return $editBtn;
    }
}
