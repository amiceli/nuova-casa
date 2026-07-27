<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingIsCompleted {
    /**
     * The onboarding and what it calls stay reachable.
     *
     * @var array<int, string>
     */
    private const OPEN_PATHS = array(
        'onboarding',
        'onboarding/*',
        'api/*',
        'broadcasting/*',
        'logout',
        'login',
        'auth/*',
        'up',
    );

    /**
     * Takes a new user back to the onboarding, only while they are browsing.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $user = $request->user();

        if (! $user || $user->hasCompletedOnboarding()) {
            return $next($request);
        }

        if (! $request->isMethod('GET') || $request->expectsJson()) {
            return $next($request);
        }

        if ($request->is(self::OPEN_PATHS)) {
            return $next($request);
        }

        return redirect()->route('onboarding');
    }
}
