<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->application_status !== 'approved' && Auth::user()->account_status !== 'inactive') {
            Auth::logout();
            return redirect('/login')->withErrors(['account' => 'Your account is not approved or is inactive yet.']);
        }
        
        return $next($request);
    }
}
