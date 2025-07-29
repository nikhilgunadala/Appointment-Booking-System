<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffAuthController extends Controller
{
    // Show Staff Login Form
    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    // Handle Staff Login
   // In StaffAuthController.php

// In StaffAuthController.php

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Find the user by email first
    $user = User::where('email', $credentials['email'])->first();

    // 1. Check if a staff user with this email exists
    if (!$user || !in_array($user->role, ['doctor', 'admin'])) {
        return back()->withErrors([
            'email' => 'No staff account was found with this email address.',
        ])->onlyInput('email');
    }

    // 2. If the user exists, now check if the password is correct
    if (!Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors([
            'password' => 'The password you entered is incorrect.',
        ])->onlyInput('email');
    }

    // If both checks pass, log the user in
    Auth::login($user);
    $request->session()->regenerate();
    return redirect()->intended(route('staff.dashboard.router'));
}

    // Show Staff Registration Form
    public function showRegistrationForm()
    {
        return view('staff.auth.register');
    }

    // Handle Staff Registration
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:doctor,admin',
        'phone_number' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'date_of_birth' => 'required|date',
        'gender' => 'required|string|in:male,female,other', // <-- ADDED THIS LINE
        'specialty' => 'nullable|required_if:role,doctor|string|max:255',
        'license_number' => 'nullable|required_if:role,doctor|string|max:255',
        'experience_years' => 'nullable|required_if:role,doctor|integer|min:0',
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'role' => $validatedData['role'],
        'phone_number' => $validatedData['phone_number'],
        'address' => $validatedData['address'],
        'date_of_birth' => $validatedData['date_of_birth'],
        'gender' => $validatedData['gender'], // <-- ADDED THIS LINE
        'specialty' => $validatedData['specialty'] ?? null,
        'license_number' => $validatedData['license_number'] ?? null,
        'experience_years' => $validatedData['experience_years'] ?? null,
    ]);

    Auth::login($user);

    return redirect()->route('staff.dashboard.router');
}
}