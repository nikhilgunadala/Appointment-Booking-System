<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
  // In app/Http/Controllers/DoctorDashboardController.php

public function index()
{
    $doctor = Auth::user();
    $appointments = Appointment::with('patient')->where('doctor_id', $doctor->id)->get();

    // The stats calculation remains the same
    $stats = [
        'total' => $appointments->count(),
        'upcoming' => $appointments->whereIn('status', ['scheduled', 'confirmed'])->count(),
        'completed' => $appointments->where('status', 'completed')->count(),
        'patients_today' => $appointments->where('appointment_date', '>=', Carbon::today()->startOfDay())
                                        ->where('appointment_date', '<=', Carbon::today()->endOfDay())
                                        ->where('status', '!=', 'cancelled')
                                        ->count(),
    ];

    // --- UPDATED LOGIC FOR TODAY'S SCHEDULE ---
    // Now, it only gets appointments for today that are still active.
    $todaysSchedule = $appointments->where('appointment_date', '>=', Carbon::today()->startOfDay())
                                ->where('appointment_date', '<=', Carbon::today()->endOfDay())
                                ->whereIn('status', ['scheduled', 'confirmed']) // This is the new filter
                                ->sortBy('appointment_date');

    return view('staff.doctor.dashboard', compact('doctor', 'stats', 'todaysSchedule'));
}

// In DoctorDashboardController.php

public function getAppointmentsForDate(Request $request)
{
    $request->validate(['date' => 'required|date_format:Y-m-d']);
    $date = Carbon::parse($request->date);

    $appointments = Appointment::with('patient')
        ->where('doctor_id', Auth::id())
        ->whereDate('appointment_date', $date)
        ->whereIn('status', ['scheduled', 'confirmed'])
        ->orderBy('appointment_date')
        ->get();

    return response()->json($appointments);
}
}