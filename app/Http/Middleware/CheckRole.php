<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // if (!Auth::check()) {
        //     return redirect()->route('login')->with('error', 'Please login to access this resource.');
        // }

        $user = Auth::user();
        $userRole = $user->userRole->role_name;

        if (!in_array($userRole, $roles)) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);

            // Log unauthorized access attempt
            return redirect()->route('role_error')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
