<?php

namespace App\Models;

use App\Enums\Day;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkingHour extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'working_hours';
    protected $fillable = ['day', 'start_time', 'end_time'];
    protected $casts = [
        'day' => Day::class,
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}
