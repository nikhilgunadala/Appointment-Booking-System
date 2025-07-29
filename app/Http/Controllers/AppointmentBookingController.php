<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\Unavailability;

class AppointmentBookingController extends Controller
{
    // Step 1: Show Patient Info
    public function createStepOne(Request $request)
    {
        // Clear session data from previous bookings
        $request->session()->forget('booking');
        $patient = auth()->user();
        $request->session()->put('booking.patient', $patient);
        return view('patient.book.step-one', compact('patient'));
    }

    // Step 2: Show Doctor Selection
    public function createStepTwo(Request $request)
    {
        $doctors = User::where('role', 'doctor')->get();
        $booking = $request->session()->get('booking');
        return view('patient.book.step-two', compact('doctors', 'booking'));
    }

    // POST Step 2: Store selected doctor
    public function storeStepTwo(Request $request)
    {
        $validated = $request->validate(['doctor_id' => 'required|exists:users,id']);
        $doctor = User::find($validated['doctor_id']);
        $request->session()->put('booking.doctor', $doctor);
        return redirect()->route('patient.book.create.step.three');
    }

    // Step 3: Show Date & Time Selection
   // In app/Http/Controllers/AppointmentBookingController.php

public function createStepThree(Request $request)
{
    $booking = $request->session()->get('booking');
    $today = Carbon::today();

    // For simplicity, we'll generate a static list of available time slots.
    // In a real app, you would query the doctor's schedule for this date.
    $availableTimeSlots = [
        '09:00', '10:30', '14:00', '15:30', '16:30'
    ];
    
    return view('patient.book.step-three', [
        'booking' => $booking,
        'today' => $today,
        'availableTimeSlots' => $availableTimeSlots,
    ]);
}
    // POST Step 3: Store selected date & time
    public function storeStepThree(Request $request)
    {
        $validated = $request->validate(['appointment_time' => 'required']);
        $request->session()->put('booking.appointment_time', $validated['appointment_time']);
        return redirect()->route('patient.book.create.step.four');
    }

    // Step 4: Show Confirmation Page
    public function createStepFour(Request $request)
    {
        $booking = $request->session()->get('booking');
        return view('patient.book.step-four', compact('booking'));
    }

    // Final POST: Store the appointment
   // In AppointmentBookingController.php -> store()
public function store(Request $request)
{
    $booking = $request->session()->get('booking');
    $appointmentTime = \Carbon\Carbon::parse($booking['appointment_time']);

    // Final check to prevent double booking
    $isAlreadyBooked = Appointment::where('doctor_id', $booking['doctor']->id)
        ->where('appointment_date', $appointmentTime)
        ->where('status', '!=', 'cancelled')
        ->exists();

    if ($isAlreadyBooked) {
        return redirect()->route('patient.book.create.step.three')
            ->withErrors(['appointment_time' => 'Sorry, this time slot was just booked. Please select a different time.']);
    }

    // This is the full, correct code to create the appointment
    $appointment = Appointment::create([
        'patient_id' => $booking['patient']->id,
        'doctor_id' => $booking['doctor']->id,
        'doctor_name' => $booking['doctor']->name,
        'doctor_specialty' => $booking['doctor']->specialty,
        'appointment_date' => $appointmentTime,
        'status' => 'scheduled',
        'reason' => $request->input('reason'),
    ]);

    // Store the new appointment's ID in the session for the PDF download
    $request->session()->put('last_booked_appointment_id', $appointment->id);

    $request->session()->forget('booking');
    
    // Redirect to the new confirmation page route
    return redirect()->route('patient.book.confirmation');
}

    // Show success page
   public function confirmation(Request $request)
{
    // Get the appointment ID from the session
    $appointmentId = $request->session()->get('last_booked_appointment_id');

    // Security check: If there's no ID, redirect to the dashboard
    if (!$appointmentId) {
        return redirect()->route('patient.dashboard');
    }

    // Pass the ID to the view
    return view('patient.book.confirmation', [
        'appointmentId' => $appointmentId
    ]);
}

    // In AppointmentBookingController.php

public function getAvailableSlots(Request $request, User $doctor)
{
    $request->validate(['date' => 'required|date_format:Y-m-d']);
    $date = Carbon::parse($request->date);

    // 1. Generate all possible time slots for a standard workday
    $startTime = $date->copy()->setHour(9);
    $endTime = $date->copy()->setHour(23);
    $allSlots = [];
    while ($startTime < $endTime) {
        $allSlots[] = $startTime->format('H:i');
        $startTime->addMinutes(30);
    }

    // 2. Get all appointments already booked for this doctor on this day
    $bookedSlots = Appointment::where('doctor_id', $doctor->id)
        ->whereDate('appointment_date', $date)
        ->where('status', '!=', 'cancelled')
        ->get()->pluck('appointment_date')->map(fn($dt) => Carbon::parse($dt)->format('H:i'))->toArray();

    // 3. NEW: Get all unavailable time blocks for this doctor on this day
    $unavailabilities = Unavailability::where('doctor_id', $doctor->id)
        ->where('start_time', '<=', $date->copy()->endOfDay())
        ->where('end_time', '>=', $date->copy()->startOfDay())
        ->get();

    $unavailableSlots = [];
    foreach ($unavailabilities as $unavailability) {
        $start = Carbon::parse($unavailability->start_time);
        $end = Carbon::parse($unavailability->end_time);
        // Generate all 30-minute slots covered by this unavailability block
        while ($start < $end) {
            $unavailableSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
    }

    // 4. Combine booked and unavailable slots, then find the difference
    $blockedSlots = array_unique(array_merge($bookedSlots, $unavailableSlots));
    $availableSlots = array_diff($allSlots, $blockedSlots);

    return response()->json(array_values($availableSlots));
}
}