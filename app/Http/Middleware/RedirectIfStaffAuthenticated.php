<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfStaffAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // If the user is a staff member, redirect them to the staff dashboard.
            if (in_array(Auth::user()->role, ['doctor', 'admin'])) {
                return redirect()->route('staff.dashboard.router');
            }
        }

        return $next($request);
    }
}