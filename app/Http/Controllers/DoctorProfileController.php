<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules as PasswordRules;

class DoctorProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('staff.doctor.profile', ['user' => $request->user()]);
    }

   // In app/Http/Controllers/DoctorProfileController.php

public function update(Request $request)
{
    $user = Auth::user();
    $validatedData = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'phone_number' => ['required', 'string', 'max:20'],
        'address' => ['required', 'string', 'max:500'],
        'date_of_birth' => ['required', 'date'],
        'gender' => ['required', 'string', 'in:male,female,other'], // <-- ADDED THIS LINE
        'specialty' => ['required', 'string', 'max:255'],
        'license_number' => ['required', 'string', 'max:255'],
        'experience_years' => ['required', 'integer', 'min:0'],
    ]);
    $user->fill($validatedData);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }
    $user->save();

    return Redirect::route('doctor.profile.edit')->with('status', 'profile-updated');
}

    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', PasswordRules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}