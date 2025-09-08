<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Let unauthenticated requests pass (login, register, etc.),
        // 'auth' middleware will handle protection where required.
        if (!$user) {
            return $next($request);
        }

        // Hard block suspended users everywhere in 'web' group
        if ((int) ($user->is_active ?? 0) !== 1) {
            // Optional: flush session to ensure no lingering auth
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Your account is suspended.');
        }

        return $next($request);
    }
}
