<?php

namespace Backstage\LaravelUsers\Listeners\Request;

use Backstage\LaravelUsers\Events\Request\WebTrafficDetected;

class RecordUserMovements
{
    public function handle(WebTrafficDetected $event)
    {
        $request = $event->request;

        $user = $request->user();

        $user?->traffic()->create([
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'route_name' => $request->route()?->getName(),
            'route_action' => $request->route()?->getActionName(),
            'route_parameters' => $request->route()?->parameters(),
        ]);
    }
}
