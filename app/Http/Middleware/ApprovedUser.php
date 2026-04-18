<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle($request, Closure $next)
{
    if (!auth()->user() || !auth()->user()->is_approved) {
        abort(403, 'Not approved');
    }

    return $next($request);
}
}
