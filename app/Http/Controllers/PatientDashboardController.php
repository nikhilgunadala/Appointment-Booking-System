<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $appointments = Appointment::where('patient_id', $user->id)
                                ->orderBy('appointment_date', 'asc')
                                ->get();

        $stats = [
            'total' => $appointments->count(),
            'upcoming' => $appointments->where('status', 'scheduled')->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
        ];

        // Filter for upcoming appointments only for the list
        $upcomingAppointments = $appointments->where('status', 'scheduled');

        return view('patient.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'upcomingAppointments' => $upcomingAppointments
        ]);
    }
}