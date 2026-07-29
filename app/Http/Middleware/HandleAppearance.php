<?php

namespace App\Http\Middleware;

use App\Enums\Theme;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance {
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        // the theme a user picked follows them from a browser to the next,
        // the cookie is only there for whoever is not logged in yet
        $theme = $request->user()?->theme?->value
            ?? $request->cookie('appearance')
            ?? Theme::System->value;

        View::share('appearance', $theme);

        return $next($request);
    }
}
