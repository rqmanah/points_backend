<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $response = [
            'status' => 'error',
            'data' => [],
            'message' => "Unauthorized",
        ];
        return $request->expectsJson() ? null : abort(response()->json($response, 401));
    }
}
