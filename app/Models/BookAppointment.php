<?php

namespace App\Models;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['appointment_type_id', 'name', 'phone', 'city'];

    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }
}
