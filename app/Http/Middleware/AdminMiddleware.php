<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isOperator()) {
            abort(403, 'Akses hanya untuk Admin atau Super Admin.');
        }

        return $next($request);
    }
}
