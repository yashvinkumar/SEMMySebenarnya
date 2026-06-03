<?php

namespace App\Http\Middleware;

use App\Models\UserRecord;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $guard
     */
    public function handle(Request $request, Closure $next, string $guard): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login', ['type' => $guard]);
        }

        // Check if agency user needs to reset password
        if ($guard === 'agency') {
            /** @var UserRecord $user */
            $user = Auth::guard('agency')->user();
            if ($user && $user->needsPasswordReset() && !$request->routeIs('agency.password.reset')) {
                return redirect()->route('agency.password.reset');
            }
        }

        return $next($request);
    }
}
