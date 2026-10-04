<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetCors
{
    /**
     * Allow cross-origin requests to the widget API endpoints.
     *
     * This middleware is applied only to /api/widget/* routes so external
     * sites (e.g. WordPress on tirtakepri.co.id) can embed the chat widget.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With');

        return $response;
    }
}
