<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't track admin routes, console, or during testing
        if (! $request->is('admin*') && ! app()->runningInConsole() && ! app()->environment('testing')) {
            try {
                PageView::create([
                    'ip_address' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
            } catch (\Throwable $e) {
                // Ignore tracking failures to prevent breaking user requests
            }
        }

        return $next($request);
    }
}
