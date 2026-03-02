<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsRecruiter
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, [UserRole::Admin, UserRole::Recruiter])) {
            abort(403, 'Unauthorized. Recruiter access required.');
        }

        return $next($request);
    }
}
