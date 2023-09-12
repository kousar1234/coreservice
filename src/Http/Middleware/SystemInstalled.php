<?php

namespace ThemeLooks\CoreService\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SystemInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('IS_USER_REGISTERED') != 1 && !str_contains($request->url(), 'install')) {
            return redirect('/install');
        }
        return $next($request);
    }
}
