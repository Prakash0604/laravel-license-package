<?php

namespace YourCompany\LaravelLicense\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use YourCompany\LaravelLicense\LicenseManager;

class EnsureLicenseIsValid
{
    public function __construct(
        private readonly LicenseManager $license
    ) {}

    public function handle(Request $request, Closure $next): Response {
        if (!config('license.middleware.enabled', true) || $this->excluded($request)) {
            return $next($request);
        }

        if (!$this->license->isValid()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Application license is inactive.',
                    'code' => 'LICENSE_INACTIVE',
                ], 503);
            }

            return response()->view(
                'yourcompany-license::license.inactive',
                [
                    'license' => $this->license->status()['license'] ?? null,
                ],
                503
            );
        }

        return $next($request);
    }

    private function excluded(Request $request): bool
    {
        foreach (config('license.paths.excluded', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}