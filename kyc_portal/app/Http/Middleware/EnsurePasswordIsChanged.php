<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->must_change_password
            && ! $request->routeIs('profile.*')
            && ! $request->routeIs('logout')) {
            return redirect()
                ->route('profile.edit')
                ->with('warning', 'Change your temporary password before using the portal.');
        }

        return $next($request);
    }
}
