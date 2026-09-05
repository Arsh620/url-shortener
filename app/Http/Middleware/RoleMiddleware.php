<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Check if the authenticated user has the required role.
     * Usage in routes: middleware('role:admin,member')
     * If user is not logged in or role doesn't match, return 403 Forbidden.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Deny access if user is not logged in or their role is not in the allowed list
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
