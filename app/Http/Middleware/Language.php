<?php

namespace App\Http\Middleware;

use Closure;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check for user language and change it according to it
        if (auth()->user()) {
            if (auth()->user()->lang != app()->getLocale()) {
                app()->setLocale(auth()->user()->lang);
            }
        }

        return $next($request);
    }
}
