<?php

namespace App\Models;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\BookType;
class BookAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['appointment_type_id', 'name', 'phone', 'city', 'date', 'time', 'book_type'];
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'book_type' => BookType::class,
    ];
   
    public function appointmentType()
    {
        return $this->belongsTo(AppointmentType::class);
    }
}
