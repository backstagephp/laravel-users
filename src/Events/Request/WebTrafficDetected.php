<?php

namespace Backstage\LaravelUsers\Events\Request;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class WebTrafficDetected
{
    use Dispatchable;
    use SerializesModels;

    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
