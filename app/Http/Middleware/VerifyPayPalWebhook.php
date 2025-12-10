<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPayPalWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware can be used to verify webhook signature for all webhook routes
        // Currently verification is done in the controller
        
        return $next($request);
    }
}