<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDayHour extends Model
{
    use HasFactory;
    protected $table = 'working_day_hours';
    protected $fillable = ['working_day_id', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function workingDay()
    {
        return $this->belongsTo(WorkingDay::class);
    }
}
