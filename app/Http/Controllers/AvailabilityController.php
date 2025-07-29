<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Unavailability;
use App\Models\Appointment;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function index()
    {
        return view('staff.doctor.availability');
    }

    // New method to get a detailed schedule for one day
    public function getDailySchedule(Request $request)
    {
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $date = Carbon::parse($request->date);
        $doctorId = Auth::id();

        // 1. Generate all possible time slots for a standard workday
        $startTime = $date->copy()->setHour(9);
        $endTime = $date->copy()->setHour(23);
        $allSlots = [];
        while ($startTime < $endTime) {
            $allSlots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        // 2. Get all appointments already booked
        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->get()->pluck('appointment_date')->map(fn($dt) => Carbon::parse($dt)->format('H:i'))->toArray();

        // 3. Get all unavailable time blocks
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

        // 4. Build the final schedule object
        $schedule = [];
        foreach ($allSlots as $slot) {
            $status = 'available';
            if (in_array($slot, $bookedSlots)) {
                $status = 'booked';
            } elseif (in_array($slot, $unavailableSlots)) {
                $status = 'unavailable';
            }
            $schedule[] = ['time' => $slot, 'status' => $status];
        }

        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        Unavailability::create([
            'doctor_id' => Auth::id(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Time block has been marked as unavailable.');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate(['time_to_clear' => 'required|date']);
        $timeToClear = Carbon::parse($validated['time_to_clear']);
        
        // Find and delete any unavailability block that covers this specific time slot
        Unavailability::where('doctor_id', Auth::id())
            ->where('start_time', '<=', $timeToClear)
            ->where('end_time', '>', $timeToClear)
            ->delete();

        return response()->json(['status' => 'success']);
    }
}