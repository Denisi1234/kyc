<?php

namespace App\Http\Middleware;

use App\Enums\AuditEvent;
use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            app(AuditLogger::class)->log(
                AuditEvent::AuthLogout,
                $user,
                "Blocked a request from inactive account {$user->name}.",
                [],
                ['reason' => 'inactive'],
                $user,
            );

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['identifier' => 'This account is inactive. Contact an administrator.']);
        }

        return $next($request);
    }
}
