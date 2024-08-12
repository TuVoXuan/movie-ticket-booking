<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission_name): Response
    {
        if (!auth()->user()->hasPermission($permission_name)) {
            return back()->with('error', 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
