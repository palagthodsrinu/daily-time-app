<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureUserHasRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, "Not logged in");
        }

        // Debug logging
        Log::info('Role check', [
            'user_id' => $user->id,
            'user_roles' => $user->roles->pluck('name'),
            'required_roles' => $roles,
            'has_role_check' => $user->hasRole($roles)
        ]);

        // Always allow admin to access everything
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        abort(403, "You are not authorized to access this resource. Required roles: " . implode(', ', $roles));
    }
}