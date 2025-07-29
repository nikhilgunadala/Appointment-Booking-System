<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Find the first user (our patient)
    $patient = User::where('role', 'patient')->first();

    if ($patient) {
        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_name' => 'Dr. Sarah Johnson',
            'doctor_specialty' => 'Cardiology',
            'appointment_date' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0),
            'status' => 'scheduled',
        ]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_name' => 'Dr. Michael Chen',
            'doctor_specialty' => 'Orthopedics',
            'appointment_date' => Carbon::now()->addDays(1)->setHour(10)->setMinute(30),
            'status' => 'scheduled',
        ]);
    }
    }
}
