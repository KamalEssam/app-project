<?php

namespace App\Http\Middleware;

use Closure;

class CheckAccountCompletion
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
        if (!auth()->check()) {
            auth()->logout();
            return redirect()->route('login');
        }

        if (auth()->user()->role_id == 1 && auth()->user()->account->is_completed == 0) {
            $passedRoutes = [
                [
                    'route' => 'provinces/list',
                    'method' => 'post'
                ], [
                    'route' => 'clinics',
                    'method' => 'post'
                ], [
                    'route' => 'assistants',
                    'method' => 'post'
                ], [
                    'route' => 'working-hours/reset',
                    'method' => 'delete'
                ], [
                    'route' => 'working-hours/get-deleted-reservations',
                    'method' => 'post'
                ], [
                    'route' => 'working-hours/check-all',
                    'method' => 'post'
                ], [
                    'route' => 'working-hours',
                    'method' => 'post'
                ], [
                    'route' => 'profile/{id}',
                    'method' => 'PATCH'
                ], [
                    'route' => 'sub-specialities/list',
                    'method' => 'POST'
                ]
            ];

            foreach ($passedRoutes as $route) {
                if ($request->is($route['route'] && $request->isMethod($route['method']))) {
                    return $next($request);
                }
            }
            // in case of single
            if (auth()->user()->account->type == 0) {
                return redirect()->route('account-completion');
            } else {
                // in case of poly
                return redirect()->route('poly-account-completion');
            }
        }
        return $next($request);
    }
}
