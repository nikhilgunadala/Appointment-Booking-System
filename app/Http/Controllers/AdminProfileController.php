<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules as PasswordRules;

class AdminProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile.edit', ['user' => $request->user()]);
    }

    // In AdminProfileController.php

public function update(Request $request)
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'phone_number' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string', 'max:500'],
        'date_of_birth' => ['nullable', 'date'],
        'gender' => ['nullable', 'string', 'in:male,female,other'],
    ]);

    // Handle optional password change
    if ($request->filled('password')) {
        $request->validate([
            'password' => ['required', 'confirmed', PasswordRules\Password::defaults()],
        ]);
        $validatedData['password'] = Hash::make($request->password);
    }

    $user->update($validatedData);
    return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
}
}