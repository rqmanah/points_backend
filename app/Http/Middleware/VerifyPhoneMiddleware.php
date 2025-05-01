<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyPhoneMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check is authenticated and phone is verified
        if (Auth::check() && Auth::guard('sanctum')?->user()?->phone_verified_at == null) {
            $response = [
                'status' => 'error',
                'message' => 'This Account has not been verified yet',
                'result' => null
            ];

            return response()->json($response, 400);
        }
        return $next($request);
    }
}
