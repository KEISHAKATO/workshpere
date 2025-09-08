<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspended
{
    /**
     * Runs on every web request (see Kernel::$middlewareGroups['web'])
     * If the user is logged in and suspended, end their session and redirect to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not logged in? Nothing to do here.
        if (! $user) {
            return $next($request);
        }

        // If suspended, force logout + show message.
        if ($user->is_active === false || $user->is_active === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account is suspended.'], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is suspended. Please contact support.'])
                ->onlyInput('email');
        }

        return $next($request);
    }
}
