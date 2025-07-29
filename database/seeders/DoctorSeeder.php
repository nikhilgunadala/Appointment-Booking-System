<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    User::create([
        'name' => 'Dr. Sarah Johnson', 'email' => 's.johnson@hcp.com', 'password' => Hash::make('password'),
        'role' => 'doctor', 'specialty' => 'Cardiology', 'department' => 'Internal Medicine',
        'experience_years' => 15, 'rating' => 4.8
    ]);
    User::create([
        'name' => 'Dr. Michael Chen', 'email' => 'm.chen@hcp.com', 'password' => Hash::make('password'),
        'role' => 'doctor', 'specialty' => 'Orthopedics', 'department' => 'Surgery',
        'experience_years' => 12, 'rating' => 4.9
    ]);
    User::create([
        'name' => 'Dr. Emily Rodriguez', 'email' => 'e.rodriguez@hcp.com', 'password' => Hash::make('password'),
        'role' => 'doctor', 'specialty' => 'Pediatrics', 'department' => 'General Medicine',
        'experience_years' => 8, 'rating' => 4.7
    ]);
}
}
