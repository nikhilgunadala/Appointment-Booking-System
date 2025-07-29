<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'doctor_name',
        'doctor_specialty',
        'appointment_date',
        'status',
        'reason',
    ];
     public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');

    }

    // In Appointment.php
public function patient()
{
    return $this->belongsTo(User::class, 'patient_id');
}

}

