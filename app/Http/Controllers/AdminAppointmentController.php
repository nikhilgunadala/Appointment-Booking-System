<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Unavailability;

class AdminAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor'])->whereIn('status', ['scheduled', 'confirmed'])->orderBy('appointment_date', 'desc');

        if ($request->filled('patient_name')) {
            $query->whereHas('patient', fn($q) => $q->where('name', 'like', '%' . $request->patient_name . '%'));
        }
        if ($request->filled('doctor_name')) {
            $query->whereHas('doctor', fn($q) => $q->where('name', 'like', '%' . $request->doctor_name . '%'));
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today': $query->whereDate('appointment_date', Carbon::today()); break;
                case 'upcoming': $query->where('appointment_date', '>', Carbon::now()); break;
                case 'past': $query->where('appointment_date', '<', Carbon::now()); break;
            }
        }

        $appointments = $query->paginate(10);
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();

        return view('admin.appointments.index', compact('appointments', 'doctors'));
    }

    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load(['patient', 'doctor']));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date_format:Y-m-d',
            'appointment_time' => 'required|date_format:H:i',
            'doctor_id' => 'required|exists:users,id',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled',
        ]);

        $doctor = User::find($validated['doctor_id']);
        $fullDateTime = Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']);

        // Final check to prevent double booking
        $isAlreadyBooked = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $fullDateTime)
            ->where('id', '!=', $appointment->id) // Exclude the current appointment
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($isAlreadyBooked) {
            return back()->withErrors(['error' => 'This time slot is already booked for the selected doctor.']);
        }

        $appointment->update([
            'appointment_date' => $fullDateTime,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
            'doctor_specialty' => $doctor->specialty,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);
        return redirect()->route('admin.appointments.index')->with('success', 'Appointment cancelled successfully.');
    }
    
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'doctor_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);
        $date = Carbon::parse($request->date);
        $doctorId = $request->doctor_id;
        $appointmentId = $request->appointment_id;

        // 1. Generate all possible time slots
        $startTime = $date->copy()->setHour(9);
        $endTime = $date->copy()->setHour(23);
        $allSlots = [];
        while ($startTime < $endTime) {
            $allSlots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        // 2. Get all booked appointments
        $bookedSlotsQuery = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled');
        if ($appointmentId) {
            $bookedSlotsQuery->where('id', '!=', $appointmentId);
        }
        $bookedSlots = $bookedSlotsQuery->get()->pluck('appointment_date')->map(fn($dt) => Carbon::parse($dt)->format('H:i'))->toArray();

    // 3.Get all unavailable time blocks
    $unavailabilities = Unavailability::where('doctor_id', $doctorId)
        ->where('start_time', '<=', $date->copy()->endOfDay())
        ->where('end_time', '>=', $date->copy()->startOfDay())
        ->get();

    $unavailableSlots = [];
    foreach ($unavailabilities as $unavailability) {
        $start = Carbon::parse($unavailability->start_time);
        $end = Carbon::parse($unavailability->end_time);
        while ($start < $end) {
            $unavailableSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
    }

    // 4. Combine and find available slots
    $blockedSlots = array_unique(array_merge($bookedSlots, $unavailableSlots));
    $availableSlots = array_diff($allSlots, $blockedSlots);

    return response()->json(array_values($availableSlots));
}

   

public function edit(Appointment $appointment)
{
    $doctors = User::where('role', 'doctor')->orderBy('name')->get();
    return view('admin.appointments.edit', compact('appointment', 'doctors'));
}
public function history(Request $request)
{
    $query = Appointment::with(['patient', 'doctor'])
                ->whereIn('status', ['completed', 'cancelled']) // <-- Fetches past appointments
                ->orderBy('appointment_date', 'desc');

   

    $appointments = $query->paginate(10);

    return view('admin.appointments.history', compact('appointments'));
}

}