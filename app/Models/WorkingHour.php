<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkingHour extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'working_hours';
    protected $fillable = ['date', 'start_time', 'end_time'];
    protected $dates = ['date'];
    protected $casts = [
        'date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
    ];
}
