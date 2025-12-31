<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Allow your frontend origin (change '*' to 'https://jeeran-app.pages.dev' for production)
        $response->headers->set('Access-Control-Allow-Origin', '*');

        // Allow methods, headers, etc.
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application, lang');
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization');  // Optional, add back if needed
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');  // Cache preflight for 24 hours

        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json('OK', 200, $response->headers->all());
        }

        return $response;
    }
}