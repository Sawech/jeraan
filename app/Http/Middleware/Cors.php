<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Add this import

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        // Log incoming request details
        Log::info('CORS Middleware hit', [
            'method' => $request->method(),
            'path' => $request->path(),
            'origin' => $request->header('Origin'),
        ]);

        $response = $next($request);

        // Set headers
        $response->headers->set('Access-Control-Allow-Origin', 'https://jeeran-app.pages.dev');  // Specific origin for security (change to '*' temporarily if testing)
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application, lang');
        $response->headers->set('Access-Control-Expose-Headers', 'Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        // Log the headers being set
        Log::info('CORS Headers set on response', $response->headers->all());

        // Handle preflight
        if ($request->getMethod() === 'OPTIONS') {
            Log::info('Handling OPTIONS preflight - returning 200');
            return response()->json('OK', 200, $response->headers->all());
        }

        return $response;
    }
}