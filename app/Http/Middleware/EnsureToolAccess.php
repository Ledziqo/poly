<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureToolAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($request->session()->boolean('has_tool_access') || $this->hasAdminCookie($request)) {
                return $next($request);
            }

            if (! Auth::check()) {
                return redirect()->route('login');
            }

            return redirect()->route('subscription.required');
        } catch (Throwable $exception) {
            return response("Access middleware failed:\n".$exception::class."\n".$exception->getMessage(), 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    private function hasAdminCookie(Request $request): bool
    {
        $expected = hash('sha256', (config('polyengine.admin_email') ?: 'Aesliexx@gmail.com').'|'.(config('polyengine.admin_password') ?: 'Mudi2005'));

        return hash_equals($expected, (string) $request->cookie('polyengine_admin_access', ''));
    }
}
