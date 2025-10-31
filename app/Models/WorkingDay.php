<?php

namespace App\Models;

use App\Enums\Day;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingDay extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'working_days';
    protected $fillable = ['day'];
    protected $casts = [
        'day' => Day::class,
    ];

    public function workingDayHours()
    {
        return $this->hasMany(WorkingDayHour::class);
    }
}
