<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is active
        if ($user->is_active != 1) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is not active.');
        }

        // Check if user type is 1 or 2
        if (!in_array($user->type, [1, 2])) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
