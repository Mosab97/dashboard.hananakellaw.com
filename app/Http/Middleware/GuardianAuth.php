<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GuardianAuth
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the school from route parameters
        $school = $request->route('school');

        // Check if guardian is authenticated
        if (! Session::has('guardian_auth')) {
            return redirect()->route('guardian.login', ['school' => $school->id])
                ->with('error', w('Please login to access this page.'));
        }

        // Verify that the authenticated guardian is accessing the correct school
        if (Session::get('guardian_auth.school_id') != $school->id) {
            Session::forget('guardian_auth');

            return redirect()->route('guardian.login', ['school' => $school->id])
                ->with('error', w('Invalid school access. Please login again.'));
        }

        // Check if session has expired (optional - set to 2 hours)
        $loggedInAt = Session::get('guardian_auth.logged_in_at');
        if (now()->diffInHours($loggedInAt) > 2) {
            Session::forget('guardian_auth');

            return redirect()->route('guardian.login', ['school' => $school->id])
                ->with('error', w('Your session has expired. Please login again.'));
        }

        return $next($request);
    }
}
