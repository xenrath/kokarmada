<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Member
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (! auth()->user()->isMember()) {
            abort(403);
        }

        return $next($request);
    }
}
