<?php

namespace Backstage\LaravelUsers\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Backstage\LaravelUsers\Events\Request\WebTrafficDetected;

class DetectUserTraffic
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            event(new WebTrafficDetected($request));
        }

        return $next($request);
    }
}
