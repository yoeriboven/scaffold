<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nightwatch\Facades\Nightwatch;

class ConfigureNightwatchSampling
{
    public function handle(Request $request, Closure $next)
    {
        $this->ignoreOhdearRequests($request);

        return $next($request);
    }

    private function ignoreOhdearRequests(Request $request): void
    {
        if (str_contains($request->userAgent() ?? '', 'OhDear.app')) {
            Nightwatch::sample(0);
        }
    }
}
