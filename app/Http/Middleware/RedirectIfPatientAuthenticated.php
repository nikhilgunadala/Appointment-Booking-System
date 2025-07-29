<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfPatientAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // If the user is a patient, redirect them to the patient dashboard.
            if (Auth::user()->role === 'patient') {
                return redirect()->route('patient.dashboard');
            }
        }

        return $next($request);
    }
}