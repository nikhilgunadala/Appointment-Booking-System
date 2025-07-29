<?php

namespace App\Http\Controllers;

use App\Models\Unavailability;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminAvailabilityController extends Controller
{
    public function index(User $doctor)
    {
        return view('admin.availability.index', compact('doctor'));
    }

    public function getDailySchedule(Request $request, User $doctor)
    {
        // This logic is identical to the doctor's controller, just using the passed $doctor object
        $request->validate(['date' => 'required|date_format:Y-m-d']);
        $date = Carbon::parse($request->date);
        
        $startTime = $date->copy()->setHour(9);
        $endTime = $date->copy()->setHour(23);
        $allSlots = [];
        while ($startTime < $endTime) {
            $allSlots[] = $startTime->format('H:i');
            $startTime->addMinutes(30);
        }

        $bookedSlots = Appointment::where('doctor_id', $doctor->id)->whereDate('appointment_date', $date)->where('status', '!=', 'cancelled')->get()->pluck('appointment_date')->map(fn($dt) => Carbon::parse($dt)->format('H:i'))->toArray();
        $unavailabilities = Unavailability::where('doctor_id', $doctor->id)->where('start_time', '<=', $date->copy()->endOfDay())->where('end_time', '>=', $date->copy()->startOfDay())->get();
        $unavailableSlots = [];
        foreach ($unavailabilities as $unavailability) {
            $start = Carbon::parse($unavailability->start_time);
            $end = Carbon::parse($unavailability->end_time);
            while ($start < $end) {
                $unavailableSlots[] = $start->format('H:i');
                $start->addMinutes(30);
            }
        }

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

    public function store(Request $request, User $doctor)
    {
        $validated = $request->validate(['start_time' => 'required|date', 'end_time' => 'required|date|after:start_time', 'reason' => 'nullable|string|max:255']);
        Unavailability::create(['doctor_id' => $doctor->id, 'start_time' => $validated['start_time'], 'end_time' => $validated['end_time'], 'reason' => $validated['reason']]);
        return back()->with('success', 'Time block has been marked as unavailable.');
    }

    public function destroy(Request $request, User $doctor)
    {
        $validated = $request->validate(['time_to_clear' => 'required|date']);
        $timeToClear = Carbon::parse($validated['time_to_clear']);
        Unavailability::where('doctor_id', $doctor->id)->where('start_time', '<=', $timeToClear)->where('end_time', '>', $timeToClear)->delete();
        return response()->json(['status' => 'success']);
    }
}