<?php

namespace Backstage\LaravelUsers\Http\Middleware;

use Backstage\LaravelUsers\Events\Request\WebTrafficDetected;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
