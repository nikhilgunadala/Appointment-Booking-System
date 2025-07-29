<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
// In AuthenticatedSessionController.php

public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    // ROLE CHECK FOR PATIENT PORTAL
    if (Auth::user()->role !== 'patient') {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'This login is for patients only. Please use the Staff Portal.',
        ])->onlyInput('email');
    }

    $request->session()->regenerate();

    // CORRECTED: Redirect to the new patient dashboard route
    return redirect()->intended(route('patient.dashboard'));
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
