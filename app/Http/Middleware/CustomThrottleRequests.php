<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\RateLimiter;
use \Illuminate\Http\Exceptions\ThrottleRequestsException;

class CustomThrottleRequests extends ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decaySeconds = 60, $prefix = '')
    {
        try {
            return parent::handle($request, $next, $maxAttempts, $decaySeconds, $prefix);
        } catch (ThrottleRequestsException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many attempts. Please try again later.'
                ], 429);
            }

            $key = $this->resolveRequestSignature($request);
            $retryAfter = RateLimiter::availableIn($key);
            
            return redirect()->back()->withInput($request->except('password'))
                ->withErrors(['throttle' => "Too many attempts. Please wait {$retryAfter} seconds."]);
        }
    }
}
